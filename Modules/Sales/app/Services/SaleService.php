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

        // 5. Due = grand_total - paid, floored at 0
        $dueAmount = max(0, round($grandTotal - $paidAmount, 2));

        return [
            'sub_total'   => $subTotal,
            'grand_total' => $grandTotal,
            'discount'    => $discount,
            'total_tax'   => $totalTax,
            'paid_amount' => $paidAmount,
            'due_amount'  => $dueAmount,
        ];
    }

    public function getSales()
    {
        return $this->sale->with('products', 'customer.payment', 'services', 'details', 'payment', 'saleReturns');
    }
    public function createSale(Request $request, $user, $cart): Sale
    {
        $totals = $this->recalculateTotals($cart, $request);

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

            // update stock
            $product = Product::where('id', $item['id'])->first();
            if ($product != null && $item['type'] == 'product' && $item['source'] == 1) {
                $product->stock = $product->stock - $item['qty'];
                $product->stock_status = $product->stock <= 0 ? 'out_of_stock' : 'in_stock';
                $product->save();

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

            // restore product stock
            foreach ($sale->products as $item) {
                $product = Product::where('id', $item->product_id)->first();
                if ($product != null && $item->source == 1) {
                    $product->stock = $product->stock + $item->quantity;
                    $product->stock_status = $product->stock <= 0 ? 'out_of_stock' : 'in_stock';
                    $product->save();
                }
            }



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

                // update stock
                $product = Product::where('id', $item['id'])->first();
                if ($product != null && $item['type'] == 'product' && $item['source'] == 1) {
                    $product->stock = $product->stock - $item['qty'];
                    $product->stock_status = $product->stock <= 0 ? 'out_of_stock' : 'in_stock';
                    $product->save();

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

        // restore product stock
        foreach ($sale->products as $item) {
            $product = Product::where('id', $item->product_id)->first();
            if ($product != null && $item->source == 1) {
                $product->stock = $product->stock + $item->quantity;
                $product->stock_status = $product->stock <= 0 ? 'out_of_stock' : 'in_stock';
                $product->save();
            }
        }

        // delete ledger and ledger details
        $ledgers = Ledger::where(function ($query) {
            $query->where('invoice_type', 'sale')
                  ->orWhere('invoice_type', 'Advance Deduct');
        })->where('invoice_no', $sale->invoice)->get();

        foreach ($ledgers as $ledger) {
            // Delete ledger details first
            $ledger->details()->delete();
            // Then delete the ledger
            $ledger->delete();
        }

        // delete payments
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
