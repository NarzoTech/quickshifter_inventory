<?php
namespace Modules\Purchase\app\Services;

use App\Models\Ledger;
use App\Models\Payment;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Services\TransactionLoggerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Accounts\app\Models\Account;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Product\app\Models\Product;
use Modules\Product\app\Services\ProductService;
use Modules\Purchase\app\Models\Purchase;
use Modules\Purchase\app\Models\PurchaseDetails;
use Modules\Purchase\app\Models\PurchaseReturn;
use Modules\Purchase\app\Models\PurchaseReturnDetails;
use Modules\Purchase\app\Models\PurchaseReturnType;
use Modules\Supplier\app\Models\Supplier;
use Modules\Supplier\app\Models\SupplierPayment;

class PurchaseService
{

    public function __construct(
        private Purchase $purchase,
        private PurchaseDetails $purchaseDetails,
        private ProductService $productService,
        private Supplier $supplier,
        private Warehouse $warehouse,
        private Product $product,
        private AccountsService $accountsService,
        private PurchaseReturn $purchaseReturn,
        private PurchaseReturnDetails $purchaseReturnDetials,
        private TransactionLoggerService $transactionLogger,
    ) {}

    /**
     * Recalculate purchase totals server-side. Never trust client-calculated values.
     */
    private function recalculatePurchaseTotals($request): array
    {
        $totalAmount = 0;
        $items = 0;
        $lineItems = [];

        foreach ($request->product_id as $index => $id) {
            $qty = (float) $request->quantity[$index];
            $unitPrice = (float) $request->unit_price[$index];
            $sellingPrice = (float) $request->selling_price[$index];

            $subTotal = round($qty * $unitPrice, 2);
            $profit = $unitPrice > 0
                ? round((($sellingPrice - $unitPrice) / $unitPrice) * 100, 2)
                : ($sellingPrice > 0 ? 100 : 0);

            $lineItems[$index] = [
                'sub_total' => $subTotal,
                'profit' => $profit,
            ];

            $totalAmount += $subTotal;
            $items += $qty;
        }

        $paidAmount = 0;
        foreach ($request->paid_amount as $amount) {
            $paidAmount += (float) $amount;
        }
        $paidAmount = round(min($paidAmount, $totalAmount), 2);
        $dueAmount = round(max(0, $totalAmount - $paidAmount), 2);

        return [
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'due_amount' => $dueAmount,
            'items' => $items,
            'line_items' => $lineItems,
        ];
    }

    /**
     * Validate that supplier has sufficient advance balance for advance payments.
     */
    private function validateAdvanceBalance($request): void
    {
        $advanceTotal = 0;
        foreach ($request->payment_type as $key => $type) {
            if ($type === 'advance') {
                $advanceTotal += (float) $request->paid_amount[$key];
            }
        }

        if ($advanceTotal > 0) {
            $supplier = Supplier::find($request->supplier_id);
            $availableAdvance = $supplier ? $supplier->advance : 0;
            if ($advanceTotal > $availableAdvance + 0.01) {
                throw new \Exception(
                    "Insufficient supplier advance balance. Available: " . number_format($availableAdvance, 2)
                    . ", Requested: " . number_format($advanceTotal, 2)
                );
            }
        }
    }

    /**
     * Recalculate purchase return totals and validate against original purchase.
     */
    private function recalculateReturnTotals($request, $purchaseId, $excludeReturnId = null): array
    {
        $purchaseDetails = PurchaseDetails::where('purchase_id', $purchaseId)
            ->get()
            ->keyBy('product_id');

        $existingReturns = PurchaseReturnDetails::where('purchase_id', $purchaseId);
        if ($excludeReturnId) {
            $existingReturns = $existingReturns->where('purchase_return_id', '!=', $excludeReturnId);
        }
        $existingReturns = $existingReturns->get()->groupBy('product_id');

        $returnAmount = 0;
        $lineItems = [];

        foreach ($request->product_id as $index => $productId) {
            $returnQty = (float) $request->return_quantity[$index];
            $detail = $purchaseDetails->get($productId);

            if (!$detail) {
                $product = Product::find($productId);
                throw new \Exception("Product '" . ($product->name ?? $productId) . "' not found in this purchase.");
            }

            // Check return quantity doesn't exceed what was purchased minus already returned
            $alreadyReturned = isset($existingReturns[$productId])
                ? $existingReturns[$productId]->sum('quantity')
                : 0;
            $maxReturnable = $detail->quantity - $alreadyReturned;

            if ($returnQty > $maxReturnable + 0.01) {
                $product = Product::find($productId);
                throw new \Exception(
                    "Return quantity ({$returnQty}) exceeds returnable quantity ({$maxReturnable}) for product: "
                    . ($product->name ?? $productId)
                );
            }

            // Recalculate subtotal from DB purchase price (not client value)
            $subTotal = round($returnQty * $detail->purchase_price, 2);
            $lineItems[$index] = ['return_subtotal' => $subTotal];
            $returnAmount += $subTotal;
        }

        // Validate received amount cannot exceed return amount
        $receivedAmount = (float) ($request->received_amount ?? 0);
        if ($receivedAmount > $returnAmount + 0.01) {
            throw new \Exception(
                "Received amount (" . number_format($receivedAmount, 2)
                . ") cannot exceed return amount (" . number_format($returnAmount, 2) . ")"
            );
        }
        $receivedAmount = min($receivedAmount, $returnAmount);

        return [
            'return_amount' => $returnAmount,
            'received_amount' => round($receivedAmount, 2),
            'line_items' => $lineItems,
        ];
    }

    /**
     * Validate stock availability for purchase return items.
     */
    private function validateReturnStock($request): void
    {
        foreach ($request->product_id as $index => $productId) {
            $returnQty = (float) ($request->return_quantity[$index] ?? 0);
            if ($returnQty <= 0) continue;
            $currentStock = (int) Product::where('id', $productId)->value('stock');

            if ($currentStock < $returnQty) {
                $product = Product::find($productId);
                throw new \Exception(
                    "Insufficient stock ({$currentStock}) to return {$returnQty} units of: "
                    . ($product->name ?? $productId)
                );
            }
        }
    }

    public function all()
    {
        $orderBy = request('order_by') == 'asc' ? 'asc' : 'desc';
        $purchase = $this->purchase->with('supplier.payments', 'warehouse', 'purchaseReturn')->orderBy('purchase_date', $orderBy)->orderBy('invoice_number', $orderBy);

        if (request()->has('keyword')) {
            $purchase = $purchase->where(function ($query) {
                $query->where('invoice_number', 'like', '%' . request()->keyword . '%')
                    ->orWhere('memo_no', 'like', '%' . request()->keyword . '%')
                    ->orWhere('reference_no', 'like', '%' . request()->keyword . '%')
                    ->orWhereHas('supplier', function ($q) {
                        $q->where('name', 'like', '%' . request()->keyword . '%')
                            ->orWhere('company', 'like', '%' . request()->keyword . '%');
                    });
            });
        }
        if (request()->supplier_id) {
            $purchase = $purchase->where('supplier_id', request()->supplier_id);
        }

        if (request('from_date') && request('to_date')) {
            $purchase = $purchase->whereBetween('purchase_date', [now()->parse(request('from_date')), now()->parse(request('to_date'))]);
        }
        if (request()->product_id) {
            $purchase = $purchase->whereHas('purchaseDetails', function ($q) {
                $q->where('product_id', request('product_id'));
            });
        }
        return $purchase;
    }

    public function allReturn()
    {
        return $this->purchaseReturn->with('purchase', 'returnType', 'purchaseDetails')->latest();
    }
    public function store($request)
    {
        // Server-side recalculation — never trust client-calculated totals
        $totals = $this->recalculatePurchaseTotals($request);

        // Validate advance balance before proceeding
        $this->validateAdvanceBalance($request);

        $attachment_name = null;
        if ($request->hasFile('attachment')) {
            $attachment      = $request->file('attachment');
            $attachment_name = time() . '.' . $attachment->getClientOriginalExtension();
            $attachment->move(public_path('uploads/purchase/'), $attachment_name);
        }
        $purchase                 = new Purchase();
        $invoiceNumber            = $this->genInvoiceNumber($request->purchase_date);
        $purchase->supplier_id    = $request->supplier_id;
        $purchase->warehouse_id   = $request->warehouse_id;
        $purchase->invoice_number = $invoiceNumber;
        $purchase->memo_no        = $request->memo_no;
        $purchase->reference_no   = $request->reference_no;
        $purchase->purchase_date  = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
        $purchase->items          = $totals['items'];
        $purchase->attachment     = $attachment_name;
        $purchase->total_amount   = $totals['total_amount'];
        $purchase->paid_amount    = $totals['paid_amount'];
        $purchase->due_amount     = $totals['due_amount'];
        $purchase->payment_status = $totals['due_amount'] == 0 ? 'paid' : 'due';
        $purchase->payment_type   = $request->payment_type;
        $purchase->note           = $request->note;
        $purchase->created_by     = Auth::id();
        $purchase->save();

        // Merge server-generated invoice number into request for downstream methods
        $request->merge(['invoice_number' => $invoiceNumber]);

        // Calculate cash-only paid (exclude advance) for ledger display
        $cashPaid = 0;
        foreach ($request->payment_type as $key => $type) {
            if ($type !== 'advance') {
                $cashPaid += (float) $request->paid_amount[$key];
            }
        }
        $cashPaid = min($cashPaid, $totals['total_amount']);
        $cashDue = $totals['total_amount'] - $cashPaid;

        $purchaseLedger = $this->purchaseLedger($request, $purchase->id, $cashPaid, $totals['total_amount'], 'purchase', 1, $cashDue);

        foreach ($request->product_id as $index => $id) {
            $lineItem = $totals['line_items'][$index];

            $purchaseDetails                 = new PurchaseDetails();
            $purchaseDetails->purchase_id    = $purchase->id;
            $purchaseDetails->product_id     = $id;
            $purchaseDetails->quantity       = $request->quantity[$index];
            $purchaseDetails->purchase_price = $request->unit_price[$index];
            $purchaseDetails->sale_price     = $request->selling_price[$index];
            $purchaseDetails->sub_total      = $lineItem['sub_total'];
            $purchaseDetails->profit         = $lineItem['profit'];
            $purchaseDetails->created_by     = Auth::id();
            $purchaseDetails->save();

            // Use DB-level increment to bypass Product::getStockAttribute number_format issue
            $qty = (int) $request->quantity[$index];
            Product::where('id', $id)->update([
                'stock' => DB::raw("stock + {$qty}"),
                'cost'  => $request->unit_price[$index],
                'price' => $request->selling_price[$index],
            ]);
            $product = Product::find($id);

            // create stock
            Stock::create([
                'purchase_id'    => $purchase->id,
                'product_id'     => $id,
                'date'           => Carbon::createFromFormat('d-m-Y', $request->purchase_date),
                'type'           => 'Purchase',
                'invoice'        => route('admin.purchase.invoice', $purchase->id),
                'in_quantity'    => $request->quantity[$index],
                'sku'            => $product->sku,
                'purchase_price' => $request->unit_price[$index],
                'sale_price'     => $request->selling_price[$index],
                'rate'           => $request->unit_price[$index],
                'profit'         => 0,
                'created_by'     => auth('admin')->user()->id,
            ]);
        }

        // create payments
        foreach ($request->payment_type as $key => $item) {
            $payAmount = (float) ($request->paid_amount[$key] ?? 0);
            if ($payAmount <= 0) continue;

            $account = Account::where('account_type', $item);
            if ($item == 'cash' || $item == 'advance') {
                $account = $account->first();
                if (!$account) {
                    $account = Account::create(['account_type' => $item]);
                }
            } else {
                $accountId = $request->account_id[$key] ?? null;
                $account = $accountId ? $account->where('id', $accountId)->first() : null;
            }

            if (!$account) {
                throw new \Exception("Payment account not found for payment type: {$item}. Please check account settings.");
            }

            // Create advance deduct ledger entry first so we can link it
            $paymentLedgerId = $purchaseLedger->id;
            if ($item == 'advance') {
                $advanceLedger = new Ledger();
                $advanceLedger->supplier_id = $request->supplier_id;
                $advanceLedger->amount = $payAmount;
                $advanceLedger->total_amount = 0;
                $advanceLedger->due_amount = -$payAmount;
                $advanceLedger->invoice_type = 'Advance Deduct';
                $advanceLedger->is_paid = 1;
                $advanceLedger->invoice_no = $request->invoice_number;
                $advanceLedger->date = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
                $advanceLedger->created_by = auth('admin')->user()->id;
                $advanceLedger->save();
                $paymentLedgerId = $advanceLedger->id;
            }

            SupplierPayment::create([
                'payment_type' => $item == 'advance' ? 'advance_deduct' : 'purchase',
                'purchase_id'  => $purchase->id,
                'is_paid'      => 1,
                'supplier_id'  => $request->supplier_id,
                'account_id'   => $account->id,
                'amount'       => $payAmount,
                'payment_date' => Carbon::createFromFormat('d-m-Y', $request->purchase_date),
                'note'         => $request->note,
                'created_by'   => auth('admin')->user()->id,
                'account_type' => accountList()[$item] ?? $item,
                'invoice'      => $request->invoice_number,
                'ledger_id'    => $paymentLedgerId,
            ]);
        }

        // Log purchase transaction
        $this->transactionLogger->logPurchase('create', $request->all(), $purchase);

        return $purchase;
    }

    public function update($request, $id)
    {
        $purchase = $this->purchase->find($id);

        // Server-side recalculation — never trust client-calculated totals
        $totals = $this->recalculatePurchaseTotals($request);

        // Calculate how much advance this purchase currently uses (will be released on delete)
        $oldAdvanceUsed = SupplierPayment::where('purchase_id', $id)
            ->where('payment_type', 'advance_deduct')
            ->sum('amount');

        // Validate advance balance accounting for the old advance being released
        $newAdvanceRequested = 0;
        foreach ($request->payment_type as $key => $type) {
            if ($type === 'advance') {
                $newAdvanceRequested += (float) $request->paid_amount[$key];
            }
        }
        if ($newAdvanceRequested > 0) {
            $supplier = Supplier::find($request->supplier_id);
            $availableAdvance = ($supplier ? $supplier->advance : 0) + $oldAdvanceUsed;
            if ($newAdvanceRequested > $availableAdvance + 0.01) {
                throw new \Exception(
                    "Insufficient supplier advance balance. Available: " . number_format($availableAdvance, 2)
                    . ", Requested: " . number_format($newAdvanceRequested, 2)
                );
            }
        }

        $attachment_name = $purchase->attachment; // Keep existing attachment by default
        if ($request->hasFile('attachment')) {
            $attachment      = $request->file('attachment');
            $attachment_name = file_upload($attachment, oldFile: $purchase->attachment);
        }

        $oldInvoiceNumber = $purchase->invoice_number;
        $oldSupplierId = $purchase->supplier_id;
        $purchaseDate = Carbon::createFromFormat('d-m-Y', $request->purchase_date);

        // Regenerate invoice number if purchase date changed
        $oldDateStr = $purchase->purchase_date ? Carbon::parse($purchase->purchase_date)->format('ymd') : '';
        $newDateStr = $purchaseDate->format('ymd');
        $newInvoiceNumber = ($oldDateStr !== $newDateStr)
            ? $this->genInvoiceNumber($request->purchase_date)
            : $oldInvoiceNumber;

        // Update invoice references in existing due payment ledger details
        if ($oldInvoiceNumber !== $newInvoiceNumber) {
            \App\Models\LedgerDetails::where('invoice', $oldInvoiceNumber)
                ->update(['invoice' => $newInvoiceNumber]);
        }

        $purchase->supplier_id    = $request->supplier_id;
        $purchase->warehouse_id   = $request->warehouse_id;
        $purchase->invoice_number = $newInvoiceNumber;
        $purchase->memo_no        = $request->memo_no;
        $purchase->reference_no   = $request->reference_no;
        $purchase->purchase_date  = $purchaseDate;
        $purchase->items          = $totals['items'];
        $purchase->attachment     = $attachment_name;
        $purchase->total_amount   = $totals['total_amount'];
        $purchase->paid_amount    = $totals['paid_amount'];
        $purchase->due_amount     = $totals['due_amount'];
        $purchase->payment_status = $totals['due_amount'] == 0 ? 'paid' : 'due';
        $purchase->payment_type   = $request->payment_type;
        $purchase->note           = $request->note;
        $purchase->updated_by     = Auth::id();
        $purchase->save();

        // Merge the new invoice number into request for downstream methods
        $request->merge(['invoice_number' => $newInvoiceNumber]);

        // Find existing ledger using the OLD supplier_id (before potential supplier change)
        // This prevents orphan ledgers when a purchase's supplier is changed
        $ledger = Ledger::where('supplier_id', $oldSupplierId)
            ->where('invoice_type', 'purchase')
            ->where('invoice_no', $oldInvoiceNumber)
            ->where('is_paid', 1)
            ->first();

        // If not found with old supplier_id, try the new one (in case they match or ledger was already updated)
        if (!$ledger) {
            $ledger = Ledger::where('supplier_id', $request->supplier_id)
                ->where('invoice_type', 'purchase')
                ->where('invoice_no', $oldInvoiceNumber)
                ->where('is_paid', 1)
                ->first();
        }

        // Calculate cash-only paid (exclude advance) for ledger display
        $cashPaid = 0;
        foreach ($request->payment_type as $key => $type) {
            if ($type !== 'advance') {
                $cashPaid += (float) $request->paid_amount[$key];
            }
        }
        $cashPaid = min($cashPaid, $totals['total_amount']);
        $cashDue = $totals['total_amount'] - $cashPaid;

        $purchaseLedger = $this->purchaseLedger($request, $purchase->id, $cashPaid, $totals['total_amount'], 'purchase', 1, $cashDue, $ledger);

        // Delete old advance deduct ledger entries using the OLD invoice number and OLD supplier_id
        Ledger::where('invoice_type', 'Advance Deduct')
            ->where('invoice_no', $oldInvoiceNumber)
            ->where('supplier_id', $oldSupplierId)
            ->delete();

        // Restore product stock using DB-level decrement (bypasses number_format accessor)
        foreach ($purchase->purchaseDetails as $purchaseDetail) {
            $qty = (int) $purchaseDetail->quantity;
            Product::where('id', $purchaseDetail->product_id)->update([
                'stock' => DB::raw("CASE WHEN stock >= {$qty} THEN stock - {$qty} ELSE 0 END"),
            ]);
        }

        // delete old purchase details
        $purchase->purchaseDetails()->delete();
        $purchase->payments()->delete();
        $purchase->stock()->delete();

        // store new purchase details with server-recalculated values
        foreach ($request->product_id as $index => $id) {
            $lineItem = $totals['line_items'][$index];

            $purchaseDetails                 = new PurchaseDetails();
            $purchaseDetails->purchase_id    = $purchase->id;
            $purchaseDetails->product_id     = $id;
            $purchaseDetails->quantity       = $request->quantity[$index];
            $purchaseDetails->purchase_price = $request->unit_price[$index];
            $purchaseDetails->sale_price     = $request->selling_price[$index];
            $purchaseDetails->sub_total      = $lineItem['sub_total'];
            $purchaseDetails->profit         = $lineItem['profit'];
            $purchaseDetails->created_by     = Auth::id();
            $purchaseDetails->save();

            // Use DB-level increment to bypass Product::getStockAttribute number_format issue
            $qty = (int) $request->quantity[$index];
            Product::where('id', $id)->update([
                'stock' => DB::raw("stock + {$qty}"),
            ]);
            $product = Product::find($id);

            // create stock
            Stock::create([
                'purchase_id'    => $purchase->id,
                'product_id'     => $id,
                'date'           => Carbon::createFromFormat('d-m-Y', $request->purchase_date),
                'type'           => 'Purchase',
                'invoice'        => route('admin.purchase.invoice', $purchase->id),
                'in_quantity'    => $request->quantity[$index],
                'sku'            => $product->sku,
                'purchase_price' => $request->unit_price[$index],
                'sale_price'     => $request->selling_price[$index],
                'rate'           => $request->unit_price[$index],
                'profit'         => 0,
                'created_by'     => auth('admin')->user()->id,
            ]);
        }

        // create payments
        foreach ($request->payment_type as $key => $item) {
            $payAmount = (float) ($request->paid_amount[$key] ?? 0);
            if ($payAmount <= 0) continue;

            $account = Account::where('account_type', $item);
            if ($item == 'cash' || $item == 'advance') {
                $account = $account->first();
                if (!$account) {
                    $account = Account::create(['account_type' => $item]);
                }
            } else {
                $accountId = $request->account_id[$key] ?? null;
                $account = $accountId ? $account->where('id', $accountId)->first() : null;
            }

            if (!$account) {
                throw new \Exception("Payment account not found for payment type: {$item}. Please check account settings.");
            }

            // Create advance deduct ledger entry first so we can link it
            $paymentLedgerId = $purchaseLedger->id;
            if ($item == 'advance') {
                $advanceLedger = new Ledger();
                $advanceLedger->supplier_id = $request->supplier_id;
                $advanceLedger->amount = $payAmount;
                $advanceLedger->total_amount = 0;
                $advanceLedger->due_amount = -$payAmount;
                $advanceLedger->invoice_type = 'Advance Deduct';
                $advanceLedger->is_paid = 1;
                $advanceLedger->invoice_no = $newInvoiceNumber;
                $advanceLedger->date = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
                $advanceLedger->created_by = auth('admin')->user()->id;
                $advanceLedger->save();
                $paymentLedgerId = $advanceLedger->id;
            }

            SupplierPayment::create([
                'payment_type' => $item == 'advance' ? 'advance_deduct' : 'purchase',
                'purchase_id'  => $purchase->id,
                'is_paid'      => 1,
                'supplier_id'  => $request->supplier_id,
                'account_id'   => $account->id,
                'amount'       => $payAmount,
                'payment_date' => Carbon::createFromFormat('d-m-Y', $request->purchase_date),
                'note'         => $request->note,
                'created_by'   => auth('admin')->user()->id,
                'invoice'      => $newInvoiceNumber,
                'account_type' => accountList()[$item] ?? $item,
                'ledger_id'    => $paymentLedgerId,
            ]);
        }

        // Log purchase transaction update
        $this->transactionLogger->logPurchase('update', $request->all(), $purchase);

        return $purchase;
    }

    public function destroy($id)
    {
        $purchase = $this->purchase->find($id);

        // Log purchase deletion before deleting
        $this->transactionLogger->logPurchase('delete', [], $purchase);

        // Restore product stock from purchase
        foreach ($purchase->purchaseDetails as $purchaseDetail) {
            $qty = (int) $purchaseDetail->quantity;
            Product::where('id', $purchaseDetail->product_id)->update([
                'stock' => DB::raw("CASE WHEN stock >= {$qty} THEN stock - {$qty} ELSE 0 END"),
            ]);
        }

        // ─── Clean up purchase returns first ───
        $purchaseReturns = PurchaseReturn::where('purchase_id', $id)->get();
        foreach ($purchaseReturns as $return) {
            // Restore return stock (items that were returned should go back to "returned" state)
            foreach ($return->purchaseDetails as $returnDetail) {
                $qty = (int) $returnDetail->quantity;
                Product::where('id', $returnDetail->product_id)->update([
                    'stock' => DB::raw("stock + {$qty}"),
                ]);
            }

            // Delete return payment and its ledger
            $returnPayment = SupplierPayment::where('purchase_return_id', $return->id)->first();
            if ($returnPayment) {
                if ($returnPayment->ledger_id) {
                    $returnLedger = Ledger::find($returnPayment->ledger_id);
                    if ($returnLedger) {
                        $returnLedger->details()->delete();
                        $returnLedger->delete();
                    }
                }
                $returnPayment->delete();
            }

            // Delete return ledger entries (in case not linked via payment)
            $returnLedgers = Ledger::where('invoice_no', $return->invoice)
                ->where('invoice_type', 'purchase return')
                ->get();
            foreach ($returnLedgers as $rl) {
                $rl->details()->delete();
                $rl->delete();
            }

            // Delete return details and stock records
            $return->purchaseDetails()->delete();
            $return->stock()->delete();
            $return->delete();
        }

        // ─── Clean up purchase details and stock ───
        PurchaseDetails::where('purchase_id', $id)?->delete();
        Stock::where('purchase_id', $id)?->delete();

        // ─── Clean up due_pay payment ledger entries ───
        $duePayPayments = SupplierPayment::where('purchase_id', $id)
            ->where('payment_type', 'due_pay')
            ->get();

        foreach ($duePayPayments as $payment) {
            if ($payment->ledger_id) {
                $paymentLedger = Ledger::find($payment->ledger_id);
                if ($paymentLedger) {
                    $otherCount = SupplierPayment::where('ledger_id', $paymentLedger->id)
                        ->where('id', '!=', $payment->id)
                        ->count();

                    if ($otherCount == 0) {
                        $paymentLedger->details()->delete();
                        $paymentLedger->delete();
                    } else {
                        $paymentLedger->details()->where('invoice', $purchase->invoice_number)->delete();
                        $rawAmount = (float) DB::table('ledgers')->where('id', $paymentLedger->id)->value('amount');
                        $rawDue = (float) DB::table('ledgers')->where('id', $paymentLedger->id)->value('due_amount');
                        DB::table('ledgers')->where('id', $paymentLedger->id)->update([
                            'amount'     => round($rawAmount - $payment->amount, 2),
                            'due_amount' => round($rawDue + $payment->amount, 2),
                        ]);
                    }
                }
            }
        }

        // ─── Delete all payments for this purchase ───
        SupplierPayment::where('purchase_id', $id)?->delete();

        // ─── Delete purchase ledger and advance deduct ledger entries ───
        $ledgers = Ledger::where('supplier_id', $purchase->supplier_id)
            ->where(function ($query) {
                $query->where('invoice_type', 'purchase')
                      ->orWhere('invoice_type', 'purchase payment')
                      ->orWhere('invoice_type', 'Advance Deduct');
            })->where('invoice_no', $purchase->invoice_number)->get();

        foreach ($ledgers as $ledger) {
            $ledger->details()->delete();
            $ledger->delete();
        }

        // ─── Also clean up any orphaned ledger details referencing this invoice ───
        \App\Models\LedgerDetails::where('invoice', $purchase->invoice_number)
            ->whereDoesntHave('ledger')
            ->delete();

        return $purchase->delete();
    }

    public function genInvoiceNumber($date = null)
    {
        return generateInvoiceNumber(Purchase::class, 'invoice_number', 'P', [], $date);
    }

    public function getPurchase($id)
    {
        return $this->purchase->with('supplier.payments', 'warehouse', 'purchaseDetails.product', 'payments')->find($id);
    }

    public function getPurchaseDetails($id)
    {
        return PurchaseDetails::with('product')->where('purchase_id', $id)->get();
    }

    public function getPurchaseList()
    {
        return $this->purchase->with('supplier', 'warehouse')->latest()->get();
    }

    public function getSuppliers()
    {
        return Supplier::where('status', 1)->with(['payments', 'purchases', 'purchaseReturn'])->orderBy('name', 'asc')->get();
    }

    public function getWarehouses()
    {
        return Warehouse::where('status', 1)->latest()->get();
    }

    public function getProducts(Request $request)
    {
        $products = $this->productService->allActiveProducts($request);
        return $products->get();
    }

    public function getAccounts()
    {
        return $this->accountsService->all()->get();
    }

    public function getPurchaseById($id)
    {
        return $this->purchase->find($id);
    }

    public function getReturnTypes()
    {
        return PurchaseReturnType::all();
    }
    public function storeReturn(Request $request, $id)
    {
        // Server-side recalculation and validation for purchase returns
        $returnTotals = $this->recalculateReturnTotals($request, $request->purchase_id);

        // Validate stock availability before returning
        $this->validateReturnStock($request);

        // store purchase return with server-calculated amounts
        $purchase = $this->purchaseReturn->create([
            'supplier_id'     => $request->supplier_id,
            'warehouse_id'    => $request->warehouse_id,
            'created_by'      => auth()->user()->id,
            'purchase_id'     => $request->purchase_id,
            'return_type_id'  => $request->return_type_id,
            'return_date'     => Carbon::createFromFormat('d-m-Y', $request->return_date),
            'note'            => $request->note,
            'payment_method'  => $request->payment_type,
            'received_amount' => $returnTotals['received_amount'],
            'return_amount'   => $returnTotals['return_amount'],
            'shipping_cost'   => $request->shipping_cost,
            'invoice'         => $this->returnInvoice($request->return_date),
        ]);

        // store purchase return details with server-calculated subtotals

        foreach ($request->product_id as $index => $val) {
            if (empty($request->return_quantity[$index]) || $request->return_quantity[$index] == 0) continue;

            $lineItem = $returnTotals['line_items'][$index];

            $purchase->purchaseDetails()->create([
                'product_id'  => $val,
                'purchase_id' => $request->purchase_id,
                'quantity'    => $request->return_quantity[$index],
                'total'       => $lineItem['return_subtotal'],
            ]);

            // Update product stock using DB-level decrement
            $qty = (int) $request->return_quantity[$index];
            Product::where('id', $val)->update([
                'stock' => DB::raw("CASE WHEN stock >= {$qty} THEN stock - {$qty} ELSE 0 END"),
            ]);
            $prod = Product::find($val);

            // update stock
            Stock::create([
                'invoice_number'     => $purchase->invoice,
                'purchase_return_id' => $purchase->id,
                'type'               => 'purchase return',
                'product_id'         => $val,
                'date'               => now(),
                'out_quantity'       => $request->return_quantity[$index],
                'sku'                => $prod->sku,
                'created_by'         => auth('admin')->user()->id,
            ]);
        }

        // Always create ledger entry for purchase return
        // Return reduces due by the full return amount (goods returned = less owed)
        $returnDue = $returnTotals['return_amount'] - $returnTotals['received_amount'];
        $ledger = $this->purchaseReturnLedger(
            $request,
            $purchase->id,
            -$returnTotals['received_amount'],
            'purchase return',
            0,
            -$returnDue,
            null,
            -$returnTotals['return_amount'],
            $purchase->invoice
        );

        // Only create payment if received_amount > 0
        if ($returnTotals['received_amount'] > 0) {
            $account = Account::where('account_type', $request->payment_type);
            if ($request->payment_type == 'cash') {
                $account = $account->first();
            } else {
                $account = $account->where('id', $request->account_id)->first();
            }

            SupplierPayment::create([
                'payment_type'       => 'purchase_receive',
                'purchase_return_id' => $purchase->id,
                'supplier_id'        => $purchase->supplier_id,
                'account_id'         => $account->id,
                'is_received'        => 1,
                'account_type'       => accountList()[$request->payment_type],
                'amount'             => $returnTotals['received_amount'],
                'payment_date'       => now(),
                'created_by'         => auth()->user()->id,
                'ledger_id'          => $ledger->id,
            ]);
        }

        return $purchase;
    }

    public function updateReturn($request, $id)
    {
        $return = $this->purchaseReturn->find($id);
        $oldInvoice = $return->invoice;

        // Regenerate invoice if return date changed
        $oldDateStr = $return->return_date ? Carbon::parse($return->return_date)->format('ymd') : '';
        $newDateStr = Carbon::createFromFormat('d-m-Y', $request->return_date)->format('ymd');
        $newInvoice = ($oldDateStr !== $newDateStr)
            ? $this->returnInvoice($request->return_date)
            : $oldInvoice;

        // Update invoice references in existing ledger details
        if ($oldInvoice !== $newInvoice) {
            \App\Models\LedgerDetails::where('invoice', $oldInvoice)
                ->update(['invoice' => $newInvoice]);
        }

        // Restore product stock from old return FIRST (DB-level increment)
        foreach ($return->purchaseDetails as $purchaseDetail) {
            $qty = (int) $purchaseDetail->quantity;
            Product::where('id', $purchaseDetail->product_id)->update([
                'stock' => DB::raw("stock + {$qty}"),
            ]);
        }

        // Server-side recalculation and validation AFTER stock is restored
        $returnTotals = $this->recalculateReturnTotals($request, $request->purchase_id, $return->id);

        // Validate stock availability for the new return quantities
        $this->validateReturnStock($request);

        $return->update([
            'supplier_id'     => $request->supplier_id,
            'warehouse_id'    => $request->warehouse_id,
            'return_type_id'  => $request->return_type_id,
            'return_date'     => Carbon::createFromFormat('d-m-Y', $request->return_date),
            'note'            => $request->note,
            'payment_method'  => $request->payment_type,
            'received_amount' => $returnTotals['received_amount'],
            'return_amount'   => $returnTotals['return_amount'],
            'shipping_cost'   => $request->shipping_cost,
            'invoice'         => $newInvoice,
        ]);

        // delete old purchase return details, payment, ledger, and stock
        $return->purchaseDetails()->delete();

        // delete old payment and its ledger (check both correct and incorrectly saved purchase_return_id)
        $oldPayments = SupplierPayment::where('payment_type', 'purchase_receive')
            ->where(function ($query) use ($return, $request) {
                $query->where('purchase_return_id', $return->id)
                      ->orWhere('purchase_return_id', $request->purchase_id);
            })
            ->get();

        foreach ($oldPayments as $oldPayment) {
            // delete ledger and ledger details
            if ($oldPayment->ledger) {
                $oldPayment->ledger->details()->delete();
                $oldPayment->ledger->delete();
            }
            $oldPayment->delete();
        }

        // Also delete any ledger directly associated with this return
        $oldLedgers = Ledger::where('invoice_no', $oldInvoice)
            ->where('invoice_type', 'purchase return')
            ->get();
        foreach ($oldLedgers as $oldLedger) {
            $oldLedger->details()->delete();
            $oldLedger->delete();
        }

        $return->stock()->delete();

        // create new purchase return details with server-calculated subtotals
        foreach ($request->product_id as $index => $val) {
            if (empty($request->return_quantity[$index]) || $request->return_quantity[$index] == 0) continue;

            $lineItem = $returnTotals['line_items'][$index];

            $return->purchaseDetails()->create([
                'product_id'  => $val,
                'purchase_id' => $request->purchase_id,
                'quantity'    => $request->return_quantity[$index],
                'total'       => $lineItem['return_subtotal'],
            ]);

            // Update product stock using DB-level decrement
            $qty = (int) $request->return_quantity[$index];
            Product::where('id', $val)->update([
                'stock' => DB::raw("CASE WHEN stock >= {$qty} THEN stock - {$qty} ELSE 0 END"),
            ]);
            $prod = Product::find($val);

            // create new stock entries
            Stock::create([
                'invoice_number'     => $return->invoice,
                'purchase_return_id' => $return->id,
                'type'               => 'purchase return',
                'product_id'         => $val,
                'date'               => now(),
                'out_quantity'       => $request->return_quantity[$index],
                'sku'                => $prod->sku,
                'created_by'         => auth('admin')->user()->id,
            ]);
        }

        // Create ledger entry for purchase return with server-calculated amounts
        // Return reduces due by the full return amount (goods returned = less owed)
        $returnDue = $returnTotals['return_amount'] - $returnTotals['received_amount'];
        $ledger = $this->purchaseReturnLedger(
            $request,
            $return->id,
            -$returnTotals['received_amount'],
            'purchase return',
            0,
            -$returnDue,
            null,
            -$returnTotals['return_amount'],
            $return->invoice
        );

        // Only create payment if received_amount > 0
        if ($returnTotals['received_amount'] > 0) {
            $account = Account::where('account_type', $request->payment_type);
            if ($request->payment_type == 'cash') {
                $account = $account->first();
            } else {
                $account = $account->where('id', $request->account_id)->first();
            }

            SupplierPayment::create([
                'payment_type'       => 'purchase_receive',
                'purchase_return_id' => $return->id,
                'supplier_id'        => $return->supplier_id,
                'account_id'         => $account->id,
                'is_received'        => 1,
                'account_type'       => accountList()[$request->payment_type],
                'amount'             => $returnTotals['received_amount'],
                'payment_date'       => now(),
                'created_by'         => auth('admin')->user()->id,
                'ledger_id'          => $ledger->id,
            ]);
        }

        return $return;
    }

    public function getPurchaseReturn($id)
    {
        return $this->purchaseReturn->with('supplier', 'purchaseDetails')->find($id);
    }

    public function purchaseLedger($request, $id, $paid, $total_amount = 0, $type = 'purchase', $isPaid = 1, $dueAmount = 0, $ledger = null)
    {
        if ($ledger == null) {
            $ledger = new Ledger();
        }

        $ledger->supplier_id  = $request->supplier_id;
        $ledger->amount       = $paid;
        $ledger->invoice_type = $type;
        $ledger->is_paid      = $isPaid;
        $ledger->invoice_url  = route('admin.purchase.invoice', $id);
        $ledger->invoice_no   = $request->invoice_number;
        $ledger->note         = $request->note;
        $ledger->due_amount   = $dueAmount;
        $ledger->total_amount = $total_amount;
        $ledger->date         = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
        $ledger->created_by   = auth('admin')->user()->id;
        $ledger->save();

        return $ledger;
    }

    public function purchaseReturnLedger($request, $id, $paid, $type = 'purchase_return', $isPaid = 0, $dueAmount = 0, $ledger = null, $totalAmount = 0, $invoiceNo = null)
    {
        if ($ledger == null) {
            $ledger = new Ledger();
        }

        $ledger->supplier_id  = $request->supplier_id;
        $ledger->amount       = $paid;
        $ledger->total_amount = $totalAmount;
        $ledger->invoice_type = $type;
        $ledger->is_paid      = $isPaid;
        $ledger->is_received  = 1;
        $ledger->invoice_url  = route('admin.purchase.return.invoice', $id);
        $ledger->invoice_no   = $invoiceNo;
        $ledger->note         = $request->note;
        $ledger->due_amount   = $dueAmount;
        $ledger->date         = Carbon::createFromFormat('d-m-Y', $request->return_date);
        $ledger->created_by   = auth('admin')->user()->id;
        $ledger->save();

        return $ledger;
    }
    public function getLedger($request, $id, $type, $isPaid = 1)
    {
        $purchase = $this->purchase->find($id);
        $ledger   = Ledger::where('supplier_id', $request->supplier_id)
            ->where('invoice_type', $type)
            ->where('invoice_no', $purchase->invoice_number)
            ->where('is_paid', $isPaid)
            ->first();

        return $ledger;
    }
    public function updateLedger($request, $id, $paidAmount, $type = 'purchase', $isPaid = 1)
    {
        $purchase = $this->purchase->find($id);

        // check if ledger already exist

        $ledger = Ledger::where('supplier_id', $request->supplier_id)
            ->where('invoice_type', 'purchase')
            ->where('invoice_no', $purchase->invoice_number)
            ->where('is_paid', $isPaid)
            ->first();

        if (! $ledger) {
            $ledger = new Ledger();
        }

        $ledger->supplier_id  = $request->supplier_id;
        $ledger->amount       = $paidAmount;
        $ledger->invoice_type = $type;
        $ledger->is_paid      = 1;
        $ledger->invoice_url  = route('admin.purchase.invoice', $purchase->id);
        $ledger->invoice_no   = $request->invoice_number;
        $ledger->note         = $request->note;
        $ledger->due_amount   = $request->due_amount ?? 0;
        $ledger->date         = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
        $ledger->created_by   = auth('admin')->user()->id;
        $ledger->save();
    }

    public function purchaseReturnCreateLedger($request, $id, $paidAmount, $type = 'purchase', $isPaid = 1)
    {

        $this->updateLedger($request, $id, $paidAmount, $type, $isPaid);
    }

    public function deleteReturn($id)
    {
        $return = $this->purchaseReturn->find($id);

        // Restore product stock using DB-level increment
        foreach ($return->purchaseDetails as $purchaseDetail) {
            $qty = (int) $purchaseDetail->quantity;
            Product::where('id', $purchaseDetail->product_id)->update([
                'stock' => DB::raw("stock + {$qty}"),
            ]);
        }

        // delete stock records
        Stock::where('purchase_return_id', $id)->delete();

        // delete ledger and ledger details
        $ledgers = Ledger::where('invoice_type', 'purchase return')
            ->where('invoice_no', $return->invoice)
            ->get();

        foreach ($ledgers as $ledger) {
            // Delete ledger details first
            $ledger->details()->delete();
            // Then delete the ledger
            $ledger->delete();
        }

        // delete payments (check both correct and incorrectly saved purchase_return_id)
        $oldPayments = SupplierPayment::where('payment_type', 'purchase_receive')
            ->where(function ($query) use ($return) {
                $query->where('purchase_return_id', $return->id)
                      ->orWhere('purchase_return_id', $return->purchase_id);
            })
            ->get();

        foreach ($oldPayments as $oldPayment) {
            if ($oldPayment->ledger) {
                $oldPayment->ledger->details()->delete();
                $oldPayment->ledger->delete();
            }
            $oldPayment->delete();
        }

        // delete purchase return details
        $return->purchaseDetails()->delete();

        // delete the return itself
        $return->delete();
    }

    public function returnInvoice($date = null)
    {
        return generateInvoiceNumber(PurchaseReturn::class, 'invoice', 'PR', [], $date);
    }
}
