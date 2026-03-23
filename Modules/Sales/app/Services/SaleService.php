<?php

namespace Modules\Sales\app\Services;

use App\Models\Ledger;
use App\Models\Payment;
use App\Models\Stock;
use App\Services\TransactionLoggerService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Accounts\app\Models\Account;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Product\app\Models\Product;
use Modules\Product\app\Models\Variant;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;
use Modules\Service\app\Models\Service;

class SaleService
{
    public function __construct(
        private Sale $sale,
        private TransactionLoggerService $transactionLogger
    ) {}

    private function parseDate($date)
    {
        if (!$date) {
            return null;
        }

        // Try d-m-Y format first (expected from form)
        try {
            return Carbon::createFromFormat('d-m-Y', $date);
        } catch (Exception $e) {
            // Try Y-m-d format (database format)
            try {
                return Carbon::createFromFormat('Y-m-d', $date);
            } catch (Exception $e) {
                // Try parsing as general date string
                return Carbon::parse($date);
            }
        }
    }

    /**
     * Recalculate sale totals server-side from cart items.
     * Never trust frontend-calculated values for sub_total, grand_total, paid, or due.
     */
    private function recalculateTotals(array $cart, Request $request): array
    {
        // 1. Recalculate sub_total from cart items
        $subTotal = 0;
        foreach ($cart as $item) {
            $subTotal += round((float) $item['price'] * (float) $item['qty'], 2);
        }

        // 2. Discount and tax are user-specified policy values
        $discount = (float) ($request->discount_amount ?? 0);
        $totalTax = (float) ($request->total_tax ?? 0);

        // 3. Recalculate grand total
        $grandTotal = round($subTotal - $discount + $totalTax, 2);
        $grandTotal = max(0, $grandTotal);

        // 4. Sum payments, cap at grand total
        $rawPaid = array_sum($request->paying_amount ?? []);
        $paidAmount = min($rawPaid, $grandTotal);
        $excess = max(0, round($rawPaid - $grandTotal, 2));

        // 5. Due = grand_total - paid, floored at 0
        $dueAmount = max(0, round($grandTotal - $paidAmount, 2));

        return [
            'sub_total'   => $subTotal,
            'grand_total' => $grandTotal,
            'discount'    => $discount,
            'total_tax'   => $totalTax,
            'paid_amount' => $paidAmount,
            'due_amount'  => $dueAmount,
            'excess'      => $excess,
        ];
    }

    /**
     * Handle excess payment: apply to customer's outstanding dues first, then create advance.
     * For guest customers, excess is ignored (treated as change).
     */
    private function handleExcessPayment(float $excess, $user, Sale $sale, Request $request, string $accountId): void
    {
        if ($excess <= 0 || !$user) {
            return;
        }

        $remaining = $excess;
        $saleDate = $this->parseDate($request->sale_date);

        // 1. Apply excess to customer's outstanding dues (oldest first)
        $outstandingDues = CustomerDue::where('customer_id', $user->id)
            ->where('due_amount', '>', 0)
            ->where('invoice', '!=', $sale->invoice) // exclude current sale
            ->orderBy('due_date')
            ->get();

        foreach ($outstandingDues as $due) {
            if ($remaining <= 0) break;

            $apply = min($remaining, $due->due_amount);

            // Update customer_due
            $due->due_amount -= $apply;
            $due->paid_amount += $apply;
            $due->save();

            // Update the related sale
            $dueSale = Sale::where('invoice', $due->invoice)->first();
            if ($dueSale) {
                $dueSale->paid_amount += $apply;
                $dueSale->due_amount = max(0, $dueSale->grand_total - $dueSale->paid_amount);
                $dueSale->save();
            }

            // Create due receive payment record
            CustomerPayment::create([
                'sale_id'      => $dueSale ? $dueSale->id : null,
                'customer_id'  => $user->id,
                'account_id'   => $accountId,
                'payment_type' => 'due_receive',
                'is_received'  => 1,
                'amount'       => $apply,
                'payment_date' => $saleDate,
                'note'         => 'Auto due receive from overpayment on ' . $sale->invoice,
                'created_by'   => auth('admin')->user()->id,
            ]);

            // Create due receive ledger entry
            $ledger = new Ledger();
            $ledger->customer_id = $user->id;
            $ledger->amount = $apply;
            $ledger->total_amount = 0;
            $ledger->due_amount = -$apply;
            $ledger->invoice_type = 'Due Receive';
            $ledger->is_received = 1;
            $ledger->invoice_no = generateInvoiceNumber(Ledger::class, 'invoice_no', 'DRL', ['invoice_type' => 'Due Receive'], $request->sale_date);
            $ledger->date = $saleDate;
            $ledger->created_by = auth('admin')->user()->id;
            $ledger->save();
            $ledger->invoice_url = route('admin.customers.ledger-details', $ledger->id);
            $ledger->save();

            // Update the due sale's ledger entry
            $dueLedger = Ledger::where('customer_id', $user->id)
                ->where('invoice_type', 'sale')
                ->where('invoice_no', $due->invoice)
                ->first();
            if ($dueLedger) {
                $dueLedger->amount += $apply;
                $dueLedger->due_amount = max(0, $dueLedger->due_amount - $apply);
                $dueLedger->save();
            }

            $remaining -= $apply;
        }

        // 2. If still excess remaining, create advance
        if ($remaining > 0) {
            $account = Account::find($accountId);

            // Create advance ledger
            $ledger = new Ledger();
            $ledger->customer_id = $user->id;
            $ledger->amount = $remaining;
            $ledger->total_amount = 0;
            $ledger->due_amount = -$remaining;
            $ledger->invoice_type = 'Advance Received';
            $ledger->is_received = 1;
            $ledger->invoice_no = generateInvoiceNumber(Ledger::class, 'invoice_no', 'CAL', ['invoice_type' => 'Advance Received'], $request->sale_date);
            $ledger->note = 'Auto advance from overpayment on ' . $sale->invoice;
            $ledger->date = $saleDate;
            $ledger->created_by = auth('admin')->user()->id;
            $ledger->save();
            $ledger->invoice_url = route('admin.customers.ledger-details', $ledger->id);
            $ledger->save();

            // Create advance payment record
            CustomerPayment::create([
                'customer_id'  => $user->id,
                'account_id'   => $accountId,
                'payment_type' => 'advance_receive',
                'is_received'  => 1,
                'amount'       => $remaining,
                'account_type' => $account ? accountList()[$account->account_type] ?? '' : '',
                'note'         => 'Auto advance from overpayment on ' . $sale->invoice,
                'created_by'   => auth('admin')->user()->id,
                'payment_date' => $saleDate,
                'invoice'      => generateInvoiceNumber(CustomerPayment::class, 'invoice', 'CP', [], $request->sale_date),
            ]);
        }
    }

    public function getSales()
    {
        return $this->sale->with('products', 'customer.payment', 'services', 'details', 'payment', 'saleReturns');
    }
    /**
     * Validate stock availability for all products in the cart before sale.
     */
    private function validateSaleStock(array $cart): void
    {
        foreach ($cart as $item) {
            if ($item['type'] === 'product' && ($item['source'] ?? 0) == 1) {
                $currentStock = (int) Product::where('id', $item['id'])->value('stock');
                $qty = (int) $item['qty'];
                if ($currentStock < $qty) {
                    $product = Product::find($item['id']);
                    throw new \Exception(
                        "Insufficient stock for '" . ($product->name ?? $item['id'])
                        . "'. Available: {$currentStock}, Requested: {$qty}"
                    );
                }
            }
        }
    }

    public function createSale(Request $request, $user, $cart): Sale
    {
        $totals = $this->recalculateTotals($cart, $request);

        // Validate stock availability before proceeding
        $this->validateSaleStock($cart);

        $sale = new Sale();
        $sale->user_id = $user != null ?  $user->id : null;

        $sale->customer_id = $request->order_customer_id;
        $sale->warehouse_id = 1;
        $sale->quantity = 1;
        $sale->total_price = $totals['sub_total'];
        $sale->order_date = $this->parseDate($request->sale_date);
        $sale->status = 1;
        $sale->payment_status = 1;

        $sale->payment_method = json_encode($request->payment_type);
        $sale->order_discount = $totals['discount'];
        $sale->total_tax = $totals['total_tax'];
        $sale->grand_total = $totals['grand_total'];
        $sale->invoice = $this->genInvoiceNumber($request->sale_date);

        $sale->paid_amount = $totals['paid_amount'];
        $sale->receive_amount = $request->receive_amount;
        $sale->return_amount = $request->return_amount;
        $sale->due_amount = $totals['due_amount'];
        $sale->due_date = $request->due_date ? $this->parseDate($request->due_date) : null;
        $sale->sale_note = $request->remark;
        $sale->created_by = auth('admin')->id();
        $sale->save();


        $totalQty = 0;

        foreach ($cart as $item) {
            $totalQty += $item['qty'];

            $variant = isset($item['variant']) ?  Variant::where('sku', $item['sku'])->first() : null;
            $orderDetails = new ProductSale();
            $orderDetails->sale_id = $sale->id;
            $orderDetails->product_id = $item['type'] == 'product' ? $item['id'] : null;
            $orderDetails->service_id = $item['type'] == 'service' ? $item['id'] : null;
            $orderDetails->product_sku = $item['sku'];
            $orderDetails->variant_id = $variant != null ? $variant->id : null;
            $orderDetails->price = $item['price'];
            $orderDetails->source = $item['source'];
            $orderDetails->purchase_price = $item['purchase_price'];
            $orderDetails->selling_price = $item['selling_price'];
            $orderDetails->quantity = $item['qty'];
            $orderDetails->sub_total = round((float) $item['price'] * (float) $item['qty'], 2);
            $orderDetails->attributes = $variant != null ? $item['variant']['attribute'] : null;
            $orderDetails->save();

            // Update stock using DB-level decrement to bypass number_format accessor
            $product = Product::where('id', $item['id'])->first();
            if ($product != null && $item['type'] == 'product' && $item['source'] == 1) {
                $saleQty = (int) $item['qty'];
                Product::where('id', $item['id'])->update([
                    'stock' => DB::raw("CASE WHEN stock >= {$saleQty} THEN stock - {$saleQty} ELSE 0 END"),
                    'stock_status' => DB::raw("CASE WHEN stock - {$saleQty} <= 0 THEN 'out_of_stock' ELSE 'in_stock' END"),
                ]);

                // create stock
                $purchasePrice = $product->last_purchase_price ?? 0;
                Stock::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'date' => $this->parseDate($request->sale_date),
                    'type' => 'Sale',
                    'invoice' => route('admin.sales.invoice', $sale->id),
                    'invoice_number' => $sale->invoice,
                    'out_quantity' => $item['qty'],
                    'sku' => $product->sku,
                    'purchase_price' => $purchasePrice,
                    'sale_price' => $item['price'],
                    'rate' => $item['price'],
                    'profit' => ($item['price'] - $purchasePrice) * $item['qty'],
                    'created_by' => auth('admin')->user()->id,
                ]);
            }
        }

        $sale->quantity = $totalQty;
        $sale->save();


        // create payments
        $primaryAccountId = null;
        foreach ($request->payment_type as $key => $item) {
            $account = Account::where('account_type', $item);
            if ($item == 'cash' || $item == 'advance') {
                $account = $account->first();
                if (!$account) {
                    $account = Account::create(['account_type' => $item]);
                }
            } else {
                $account = $account->where('id', $request->account_id[$key])->first();
            }
            if (!$primaryAccountId && $item !== 'advance') {
                $primaryAccountId = $account->id;
            }
            $customerId = $request->order_customer_id;
            $data = [
                'payment_type' => $item == 'advance' ? 'advance_deduct' : 'sale',
                'sale_id' => $sale->id,
                'is_received' => 1,
                'customer_id' => $request->order_customer_id,
                'account_id' => $account->id,
                'amount' => $request->paying_amount[$key],
                'payment_date' => $this->parseDate($request->sale_date),
                'created_by' => auth('admin')->user()->id,
            ];
            if ($customerId == 'walk-in-customer') {
                $data['customer_id'] = null;
                $data['is_guest'] = 1;
            }
            if ($request->paying_amount[$key]) {
                CustomerPayment::create($data);
            }
        }

        // Handle excess payment: apply to dues or create advance (skip for guests)
        if ($totals['excess'] > 0 && $user && $primaryAccountId) {
            $this->handleExcessPayment($totals['excess'], $user, $sale, $request, $primaryAccountId);
        }

        // create due — use server-calculated due_amount, not frontend value
        if ($sale->due_amount > 0 && $user) {
            CustomerDue::create([
                'invoice' => $sale->invoice,
                'due_amount' => $sale->due_amount,
                'due_date' => $request->due_date ? $this->parseDate($request->due_date) : $sale->order_date,
                'status' => 1,
                'customer_id' => $user->id
            ]);
        }


        // if user is exists

        if ($user) {
            // Calculate cash-only paid (exclude advance) for ledger display
            $cashPaid = 0;
            foreach ($request->payment_type as $key => $type) {
                if ($type !== 'advance') {
                    $cashPaid += $request->paying_amount[$key];
                }
            }
            $cashPaid = min($cashPaid, $totals['grand_total']);
            $cashDue = max(0, $totals['grand_total'] - $cashPaid);

            $this->salesLedger($request, $sale, $cashPaid, $totals['grand_total'], 'sale', 1, $cashDue);

            // Create advance deduct ledger entries to offset advance credit
            foreach ($request->payment_type as $key => $item) {
                if ($item == 'advance' && $request->paying_amount[$key]) {
                    $advanceLedger = new Ledger();
                    $advanceLedger->customer_id = $request->order_customer_id;
                    $advanceLedger->amount = $request->paying_amount[$key];
                    $advanceLedger->total_amount = 0;
                    $advanceLedger->due_amount = 0;
                    $advanceLedger->invoice_type = 'Advance Deduct';
                    $advanceLedger->is_received = 1;
                    $advanceLedger->invoice_no = $sale->invoice;
                    $advanceLedger->date = $this->parseDate($request->sale_date);
                    $advanceLedger->created_by = auth('admin')->user()->id;
                    $advanceLedger->save();
                }
            }
        }

        // Log sale transaction
        $this->transactionLogger->logSale('create', array_merge($request->all(), ['cart' => $cart]), $sale);

        return $sale;
    }

    public function updateSale(Request $request, $user, $cart, $id): Sale
    {
        DB::beginTransaction();
        try {
            $sale = $this->sale->find($id);

            // Regenerate invoice if sale date changed
            $oldInvoice = $sale->invoice;
            $newDate = $this->parseDate($request->sale_date);
            $oldDateStr = $sale->order_date ? Carbon::parse($sale->order_date)->format('ymd') : '';
            $newDateStr = $newDate ? $newDate->format('ymd') : '';
            if ($oldDateStr !== $newDateStr) {
                $sale->invoice = $this->genInvoiceNumber($request->sale_date);

                // Update invoice references in existing due payment ledger details
                \App\Models\LedgerDetails::where('invoice', $oldInvoice)
                    ->update(['invoice' => $sale->invoice]);
            }

            // update sales — recalculate totals server-side
            $totals = $this->recalculateTotals($cart, $request);

            $sale->user_id = $user != null ?  $user->id : null;
            $sale->customer_id = $request->order_customer_id;
            $sale->warehouse_id = 1;
            $sale->total_price = $totals['sub_total'];
            $sale->order_date = $newDate;
            $sale->status = 1;
            $sale->payment_status = 1;

            $sale->payment_method = json_encode($request->payment_type);
            $sale->order_discount = $totals['discount'];
            $sale->total_tax = $totals['total_tax'];
            $sale->grand_total = $totals['grand_total'];
            $sale->paid_amount = $totals['paid_amount'];

            $sale->due_amount = $totals['due_amount'];
            $sale->due_date = $request->due_date ? $this->parseDate($request->due_date) : null;
            $sale->sale_note = $request->remark;
            $sale->receive_amount = $request->receive_amount;
            $sale->return_amount = $request->return_amount;
            $sale->updated_by = auth('admin')->user()->id;

            // Restore product stock using DB-level increment
            foreach ($sale->products as $item) {
                if ($item->product_id && $item->source == 1) {
                    $qty = (int) $item->quantity;
                    Product::where('id', $item->product_id)->update([
                        'stock' => DB::raw("stock + {$qty}"),
                        'stock_status' => DB::raw("CASE WHEN stock + {$qty} > 0 THEN 'in_stock' ELSE 'out_of_stock' END"),
                    ]);
                }
            }

            // Validate stock for new cart items after restoration
            $this->validateSaleStock($cart);

            // delete old details
            $sale->details()->delete();
            $sale->payment()->delete();
            $sale->customer_due()->delete();
            $sale->stock()->delete();

            $totalQty = 0;
            foreach ($cart as $item) {
                $totalQty += $item['qty'];

                $variant = isset($item['variant']) ?  Variant::where('sku', $item['sku'])->first() : null;
                $orderDetails = new ProductSale();
                $orderDetails->sale_id = $sale->id;
                $orderDetails->product_id = $item['type'] == 'product' ? $item['id'] : null;
                $orderDetails->service_id = $item['type'] == 'service' ? $item['id'] : null;
                $orderDetails->product_sku = $item['sku'];
                $orderDetails->variant_id = $variant != null ? $variant->id : null;
                $orderDetails->price = $item['price'];
                $orderDetails->source = $item['source'];
                $orderDetails->purchase_price = $item['purchase_price'];
                $orderDetails->selling_price = $item['selling_price'];
                $orderDetails->quantity = $item['qty'];
                $orderDetails->sub_total = round((float) $item['price'] * (float) $item['qty'], 2);
                $orderDetails->attributes = $variant != null ? $item['variant']['attribute'] : null;
                $orderDetails->save();

                // Update stock using DB-level decrement
                $product = Product::where('id', $item['id'])->first();
                if ($product != null && $item['type'] == 'product' && $item['source'] == 1) {
                    $saleQty = (int) $item['qty'];
                    Product::where('id', $item['id'])->update([
                        'stock' => DB::raw("CASE WHEN stock >= {$saleQty} THEN stock - {$saleQty} ELSE 0 END"),
                        'stock_status' => DB::raw("CASE WHEN stock - {$saleQty} <= 0 THEN 'out_of_stock' ELSE 'in_stock' END"),
                    ]);

                    // create stock
                    $purchasePrice = $product->last_purchase_price ?? 0;
                    Stock::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'date' => $this->parseDate($request->sale_date),
                        'type' => 'Sale',
                        'invoice' => route('admin.sales.invoice', $sale->id),
                        'invoice_number' => $sale->invoice,
                        'out_quantity' => $item['qty'],
                        'sku' => $product->sku,
                        'purchase_price' => $purchasePrice,
                        'sale_price' => $item['price'],
                        'rate' => $item['price'],
                        'profit' => ($item['price'] - $purchasePrice) * $item['qty'],
                        'created_by' => auth('admin')->user()->id,
                    ]);
                }
            }

            $sale->quantity = $totalQty;
            $sale->save();

            // Ledger and advance deduct entries only for real customers
            if ($user) {
                // Find existing ledger using OLD invoice number
                $ledger = Ledger::where('customer_id', $request->order_customer_id)
                    ->where('invoice_type', 'sale')
                    ->where('invoice_no', $oldInvoice)
                    ->where('is_received', 1)
                    ->first();

                // Calculate cash-only paid (exclude advance) for ledger display
                $cashPaid = 0;
                foreach ($request->payment_type as $key => $type) {
                    if ($type !== 'advance') {
                        $cashPaid += $request->paying_amount[$key];
                    }
                }
                $cashPaid = min($cashPaid, $totals['grand_total']);
                $cashDue = max(0, $totals['grand_total'] - $cashPaid);

                $this->salesLedger($request, $sale, $cashPaid, $totals['grand_total'], 'sale', 1, $cashDue, $ledger);

                // Delete old advance deduct ledger entries using OLD invoice number
                Ledger::where('invoice_type', 'Advance Deduct')
                    ->where('invoice_no', $oldInvoice)
                    ->where('customer_id', $request->order_customer_id)
                    ->delete();
            }

            // create payments
            $primaryAccountId = null;
            foreach ($request->payment_type as $key => $item) {
                $account = Account::where('account_type', $item);
                if ($item == 'cash' || $item == 'advance') {
                    $account = $account->first();
                    if (!$account) {
                        $account = Account::create(['account_type' => $item]);
                    }
                } else {
                    $account = $account->where('id', $request->account_id[$key])->first();
                }
                if (!$primaryAccountId && $item !== 'advance') {
                    $primaryAccountId = $account->id;
                }
                $customerId = $request->order_customer_id;
                $data = [
                    'payment_type' => $item == 'advance' ? 'advance_deduct' : 'sale',
                    'sale_id' => $sale->id,
                    'is_received' => 1,
                    'customer_id' => $request->order_customer_id,
                    'account_id' => $account->id,
                    'amount' => $request->paying_amount[$key],
                    'payment_date' => $this->parseDate($request->sale_date),
                    'created_by' => auth('admin')->user()->id,
                ];
                if ($customerId == 'walk-in-customer') {
                    $data['customer_id'] = null;
                    $data['is_guest'] = 1;
                }
                if ($request->paying_amount[$key]) {
                    CustomerPayment::create($data);
                }
            }

            // Handle excess payment: apply to dues or create advance (skip for guests)
            if ($totals['excess'] > 0 && $user && $primaryAccountId) {
                $this->handleExcessPayment($totals['excess'], $user, $sale, $request, $primaryAccountId);
            }

            // create due — use server-calculated due_amount, not frontend value
            if ($sale->due_amount > 0 && $user) {
                CustomerDue::create([
                    'invoice' => $sale->invoice,
                    'due_amount' => $sale->due_amount,
                    'due_date' => $request->due_date ? $this->parseDate($request->due_date) : $sale->order_date,
                    'status' => 1,
                    'customer_id' => $user->id
                ]);
            }

            // Create advance deduct ledger entries to offset advance credit
            if ($user) {
                foreach ($request->payment_type as $key => $item) {
                    if ($item == 'advance' && $request->paying_amount[$key]) {
                        $advanceLedger = new Ledger();
                        $advanceLedger->customer_id = $request->order_customer_id;
                        $advanceLedger->amount = $request->paying_amount[$key];
                        $advanceLedger->total_amount = 0;
                        $advanceLedger->due_amount = 0;
                        $advanceLedger->invoice_type = 'Advance Deduct';
                        $advanceLedger->is_received = 1;
                        $advanceLedger->invoice_no = $sale->invoice;
                        $advanceLedger->date = $this->parseDate($request->sale_date);
                        $advanceLedger->created_by = auth('admin')->user()->id;
                        $advanceLedger->save();
                    }
                }
            }

            // Log sale transaction update
            $this->transactionLogger->logSale('update', array_merge($request->all(), ['cart' => $cart]), $sale);

            DB::commit();
            return $sale;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollback();
            throw $ex;
        }
    }

    public function deleteSale($id): void
    {
        $sale = $this->sale->find($id);

        // Log sale deletion before deleting
        $this->transactionLogger->logSale('delete', [], $sale);

        // Restore product stock using DB-level increment
        foreach ($sale->products as $item) {
            if ($item->product_id && $item->source == 1) {
                $qty = (int) $item->quantity;
                Product::where('id', $item->product_id)->update([
                    'stock' => DB::raw("stock + {$qty}"),
                    'stock_status' => DB::raw("CASE WHEN stock + {$qty} > 0 THEN 'in_stock' ELSE 'out_of_stock' END"),
                ]);
            }
        }

        // Handle due receive payments made against this sale
        // These represent money the customer already paid — convert to advance
        $dueReceivePayments = CustomerPayment::where('sale_id', $sale->id)
            ->where('payment_type', 'due_receive')
            ->where('amount', '>', 0)
            ->get();

        $totalDueReceived = $dueReceivePayments->sum('amount');

        if ($totalDueReceived > 0 && $sale->customer_id && $sale->customer_id != 'walk-in-customer') {
            // Remove this sale's portion from due receive ledger entries
            $ledgerDetails = \App\Models\LedgerDetails::where('invoice', $sale->invoice)->get();
            foreach ($ledgerDetails as $detail) {
                $parentLedger = Ledger::find($detail->ledger_id);
                if ($parentLedger && $parentLedger->invoice_type == 'Due Receive') {
                    // Reduce the parent ledger amount by this detail's amount
                    $parentLedger->amount -= $detail->amount;
                    $parentLedger->due_amount += $detail->amount;
                    $parentLedger->save();

                    // If parent ledger has no remaining amount, delete it
                    if ($parentLedger->amount <= 0) {
                        $parentLedger->details()->delete();
                        $parentLedger->delete();
                    } else {
                        $detail->delete();
                    }
                }
            }

            // Delete the due receive payment records for this sale
            CustomerPayment::where('sale_id', $sale->id)
                ->where('payment_type', 'due_receive')
                ->delete();

            // Convert the received amount to advance for the customer
            $cashAccount = Account::where('account_type', 'cash')->first();
            if (!$cashAccount) {
                $cashAccount = Account::create(['account_type' => 'cash']);
            }

            // Create advance ledger
            $advLedger = new Ledger();
            $advLedger->customer_id = $sale->customer_id;
            $advLedger->amount = $totalDueReceived;
            $advLedger->total_amount = 0;
            $advLedger->due_amount = -$totalDueReceived;
            $advLedger->invoice_type = 'Advance Received';
            $advLedger->is_received = 1;
            $advLedger->invoice_no = generateInvoiceNumber(Ledger::class, 'invoice_no', 'CAL', ['invoice_type' => 'Advance Received']);
            $advLedger->note = 'Auto advance from deleted sale ' . $sale->invoice;
            $advLedger->date = now();
            $advLedger->created_by = auth('admin')->user()->id;
            $advLedger->save();
            $advLedger->invoice_url = route('admin.customers.ledger-details', $advLedger->id);
            $advLedger->save();

            // Create advance payment record
            CustomerPayment::create([
                'customer_id'  => $sale->customer_id,
                'account_id'   => $cashAccount->id,
                'payment_type' => 'advance_receive',
                'is_received'  => 1,
                'amount'       => $totalDueReceived,
                'account_type' => accountList()[$cashAccount->account_type] ?? '',
                'note'         => 'Auto advance from deleted sale ' . $sale->invoice,
                'created_by'   => auth('admin')->user()->id,
                'payment_date' => now(),
                'invoice'      => generateInvoiceNumber(CustomerPayment::class, 'invoice', 'CP'),
            ]);
        }

        // delete sale ledger and advance deduct ledger entries
        $ledgers = Ledger::where(function ($query) {
            $query->where('invoice_type', 'sale')
                  ->orWhere('invoice_type', 'Advance Deduct');
        })->where('invoice_no', $sale->invoice)->get();

        foreach ($ledgers as $ledger) {
            $ledger->details()->delete();
            $ledger->delete();
        }

        // delete sale payments (excludes already-deleted due_receive ones)
        $sale->payment()->delete();

        // delete due
        $sale->customer_due()->delete();

        // delete sale details
        $sale->details()->delete();

        // delete product stock
        $sale->stock()->delete();

        // delete sale
        $sale->delete();
    }

    public function genInvoiceNumber($date = null)
    {
        return generateInvoiceNumber(Sale::class, 'invoice', 'S', [], $date);
    }
    public function editSale($id)
    {
        $sale = $this->getSales()->find($id);

        foreach ($sale->details as $key => $detail) {
            $service = null;
            $product = null;
            if ($detail->product_id) {
                $product = Product::where('id', $detail->product_id)->first();
                $type = 'product';
            } else {
                $product = Service::where('id', $detail->service_id)->first();
                $type = 'service';
            }

            $attributes = $detail->attributes;
            $options = $detail->options;

            $data = array();
            $data["rowid"] = uniqid();
            $data['id'] = $service ? $service->id : $product->id;
            $data['name'] = $service ? $service->name : $product->name;
            $data['type'] = $type;
            $data['image'] = $service ? $service->singleImage : $product->image_url;
            $data['qty'] = $detail->quantity;
            $data['price'] = $detail->price;
            $data['sub_total'] = $detail->sub_total;
            $data['sku'] = $detail->product_sku;
            $data['source'] = $detail->source;
            $data['purchase_price'] = $detail->purchase_price;
            $data['selling_price'] = $detail->selling_price;

            if ($detail->variant_id) {
                $data['variant']['attribute'] =  $attributes;
                $data['variant']['options'] =  $options;
            }
            $cart_contents = session()->get('UPDATE_CART');
            $cart_contents = $cart_contents ? $cart_contents : [];
            session()->put('UPDATE_CART', [...$cart_contents, $data["rowid"] => $data]);
        }
        $cart_contents = session()->get('UPDATE_CART', []);
        return [$cart_contents, $sale];
    }

    public function getLedger($request, $id, $isPaid = 1, $type)
    {
        $sale = $this->sale->find($id);
        $ledger = Ledger::where('customer_id', $request->order_customer_id)
            ->where('invoice_type', $type)
            ->where('invoice_no', $sale->invoice)
            ->where('is_received', $isPaid)
            ->first();

        return $ledger;
    }

    public function salesLedger($request, $sale, $paid, $total_amount = 0, $type = 'sale', $isPaid = 1, $dueAmount = 0, $ledger = null)
    {
        if ($ledger == null) $ledger = new Ledger();
        $ledger->customer_id = $request->order_customer_id;
        $ledger->amount = $paid;
        $ledger->invoice_type = $type;
        $ledger->is_received = $isPaid;
        $ledger->invoice_url = route('admin.sales.invoice', $sale->id);
        $ledger->invoice_no = $sale->invoice;
        $ledger->note = $request->note;
        $ledger->due_amount = $dueAmount;
        $ledger->total_amount = $total_amount;
        $ledger->date = $this->parseDate($request->sale_date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();
    }
}
