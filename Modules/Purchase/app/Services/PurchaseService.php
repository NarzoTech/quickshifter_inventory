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
        $attachment_name = null;
        if ($request->hasFile('attachment')) {
            $attachment      = $request->file('attachment');
            $attachment_name = time() . '.' . $attachment->getClientOriginalExtension();
            $attachment->move(public_path('uploads/purchase/'), $attachment_name);
        }
        $purchase                 = new Purchase();
        $invoiceNumber            = $this->genInvoiceNumber($request->purchase_date);
        $paidAmount               = $request->total_amount - $request->due_amount;
        $purchase->supplier_id    = $request->supplier_id;
        $purchase->warehouse_id   = $request->warehouse_id;
        $purchase->invoice_number = $invoiceNumber;
        $purchase->memo_no        = $request->memo_no;
        $purchase->reference_no   = $request->reference_no;
        $purchase->purchase_date  = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
        $purchase->items          = $request->items;
        $purchase->attachment     = $attachment_name;
        $purchase->total_amount   = $request->total_amount;
        $purchase->paid_amount    = $paidAmount;
        $purchase->due_amount     = $request->due_amount;
        $purchase->payment_status = $paidAmount == $request->total_amount ? 'paid' : 'due';
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
                $cashPaid += $request->paid_amount[$key];
            }
        }
        $cashDue = $request->total_amount - $cashPaid;

        $this->purchaseLedger($request, $purchase->id, $cashPaid, $request->total_amount, 'purchase', 1, $cashDue);

        foreach ($request->product_id as $index => $id) {
            $purchaseDetails                 = new PurchaseDetails();
            $purchaseDetails->purchase_id    = $purchase->id;
            $purchaseDetails->product_id     = $id;
            $purchaseDetails->quantity       = $request->quantity[$index];
            $purchaseDetails->purchase_price = $request->unit_price[$index];
            $purchaseDetails->sale_price     = $request->selling_price[$index];
            $purchaseDetails->sub_total      = $request->total[$index];
            $purchaseDetails->profit         = $request->profit[$index];
            $purchaseDetails->created_by     = Auth::id();
            $purchaseDetails->save();

            $product = Product::find($id);
            $product->stock += $request->quantity[$index];
            $product->cost  = $request->unit_price[$index];
            $product->price = $request->selling_price[$index];
            $product->save();

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

        // if ($paidAmount) {
        //     $this->purchaseLedger($request, $purchase->id, -$paidAmount, 'purchase payment', 1, $request->due_amount);
        // }

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
            $data = [
                'payment_type' => $item == 'advance' ? 'advance_deduct' : 'purchase',
                'purchase_id'  => $purchase->id,
                'is_paid'      => 1,
                'supplier_id'  => $request->supplier_id,
                'account_id'   => $account->id,
                'amount'       => $request->paid_amount[$key],
                'payment_date' => Carbon::createFromFormat('d-m-Y', $request->purchase_date),
                'note'         => $request->note,
                'created_by'   => auth('admin')->user()->id,
                'account_type' => accountList()[$item] ?? $item,
                'invoice'      => $request->invoice_number,
            ];
            if ($request->paid_amount[$key]) {
                SupplierPayment::create($data);
            }
        }

        // Create advance deduct ledger entries to offset advance credit
        foreach ($request->payment_type as $key => $item) {
            if ($item == 'advance' && $request->paid_amount[$key]) {
                $advanceLedger = new Ledger();
                $advanceLedger->supplier_id = $request->supplier_id;
                $advanceLedger->amount = $request->paid_amount[$key];
                $advanceLedger->total_amount = 0;
                $advanceLedger->due_amount = 0;
                $advanceLedger->invoice_type = 'Advance Deduct';
                $advanceLedger->is_paid = 1;
                $advanceLedger->invoice_no = $request->invoice_number;
                $advanceLedger->date = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
                $advanceLedger->created_by = auth('admin')->user()->id;
                $advanceLedger->save();
            }
        }

        // Log purchase transaction
        $this->transactionLogger->logPurchase('create', $request->all(), $purchase);

        return $purchase;
    }

    public function update($request, $id)
    {
        $purchase = $this->purchase->find($id);

        $attachment_name = $purchase->attachment; // Keep existing attachment by default
        if ($request->hasFile('attachment')) {
            $attachment      = $request->file('attachment');
            $attachment_name = file_upload($attachment, oldFile: $purchase->attachment);
        }

        $oldInvoiceNumber = $purchase->invoice_number;
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

        $paidAmount               = $request->total_amount - $request->due_amount;
        $purchase->supplier_id    = $request->supplier_id;
        $purchase->warehouse_id   = $request->warehouse_id;
        $purchase->invoice_number = $newInvoiceNumber;
        $purchase->memo_no        = $request->memo_no;
        $purchase->reference_no   = $request->reference_no;
        $purchase->purchase_date  = $purchaseDate;
        $purchase->items          = $request->items;
        $purchase->attachment     = $attachment_name;
        $purchase->total_amount   = $request->total_amount;
        $purchase->paid_amount    = $paidAmount;
        $purchase->due_amount     = $request->due_amount;
        $purchase->payment_status = $paidAmount == $request->total_amount ? 'paid' : 'due';
        $purchase->payment_type   = $request->payment_type;
        $purchase->note           = $request->note;
        $purchase->updated_by     = Auth::id();
        $purchase->save();

        // Merge the new invoice number into request for downstream methods
        $request->merge(['invoice_number' => $newInvoiceNumber]);

        $ledger = Ledger::where('supplier_id', $request->supplier_id)
            ->where('invoice_type', 'purchase')
            ->where('invoice_no', $oldInvoiceNumber)
            ->where('is_paid', 1)
            ->first();

        // Calculate cash-only paid (exclude advance) for ledger display
        $cashPaid = 0;
        foreach ($request->payment_type as $key => $type) {
            if ($type !== 'advance') {
                $cashPaid += $request->paid_amount[$key];
            }
        }
        $cashDue = $request->total_amount - $cashPaid;

        $this->purchaseLedger($request, $purchase->id, $cashPaid, $request->total_amount, 'purchase', 1, $cashDue, $ledger);

        // Delete old advance deduct ledger entries using the OLD invoice number
        Ledger::where('invoice_type', 'Advance Deduct')
            ->where('invoice_no', $oldInvoiceNumber)
            ->where('supplier_id', $request->supplier_id)
            ->delete();

        // restore product stock
        foreach ($purchase->purchaseDetails as $purchaseDetail) {
            $product = Product::find($purchaseDetail->product_id);
            if ($product) {
                $product->stock -= $purchaseDetail->quantity;
                $product->save();
            }
        }

        // delete old purchase details
        $purchase->purchaseDetails()->delete();
        $purchase->payments()->delete();
        $purchase->stock()->delete();

        // store new purchase details
        foreach ($request->product_id as $index => $id) {
            $purchaseDetails                 = new PurchaseDetails();
            $purchaseDetails->purchase_id    = $purchase->id;
            $purchaseDetails->product_id     = $id;
            $purchaseDetails->quantity       = $request->quantity[$index];
            $purchaseDetails->purchase_price = $request->unit_price[$index];
            $purchaseDetails->sale_price     = $request->selling_price[$index];
            $purchaseDetails->sub_total      = $request->total[$index];
            $purchaseDetails->profit         = $request->profit[$index];
            $purchaseDetails->created_by     = Auth::id();
            $purchaseDetails->save();

            $product = Product::find($id);
            $product->stock += $request->quantity[$index];
            $product->save();

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
            $account = Account::where('account_type', $item);
            if ($item == 'cash' || $item == 'advance') {
                $account = $account->first();
                if (!$account) {
                    $account = Account::create(['account_type' => $item]);
                }
            } else {
                $account = $account->where('id', $request->account_id[$key])->first();
            }
            $data = [
                'payment_type' => $item == 'advance' ? 'advance_deduct' : 'purchase',
                'purchase_id'  => $purchase->id,
                'is_paid'      => 1,
                'supplier_id'  => $request->supplier_id,
                'account_id'   => $account->id,
                'amount'       => $request->paid_amount[$key],
                'payment_date' => Carbon::createFromFormat('d-m-Y', $request->purchase_date),
                'note'         => $request->note,
                'created_by'   => auth('admin')->user()->id,
                'invoice'      => $newInvoiceNumber,
                'account_type' => accountList()[$item] ?? $item,
            ];
            if ($request->paid_amount[$key]) {
                SupplierPayment::create($data);
            }
        }

        // Create advance deduct ledger entries to offset advance credit
        foreach ($request->payment_type as $key => $item) {
            if ($item == 'advance' && $request->paid_amount[$key]) {
                $advanceLedger = new Ledger();
                $advanceLedger->supplier_id = $request->supplier_id;
                $advanceLedger->amount = $request->paid_amount[$key];
                $advanceLedger->total_amount = 0;
                $advanceLedger->due_amount = 0;
                $advanceLedger->invoice_type = 'Advance Deduct';
                $advanceLedger->is_paid = 1;
                $advanceLedger->invoice_no = $newInvoiceNumber;
                $advanceLedger->date = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
                $advanceLedger->created_by = auth('admin')->user()->id;
                $advanceLedger->save();
            }
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

        // restore product stock
        foreach ($this->purchase->find($id)->purchaseDetails as $purchaseDetail) {
            $product = Product::find($purchaseDetail->product_id);
            if ($product) {
                $product->stock -= $purchaseDetail->quantity;
                $product->save();
            }
        }

        PurchaseDetails::where('purchase_id', $id)?->delete();
        Stock::where('purchase_id', $id)?->delete();
        SupplierPayment::where('purchase_id', $id)?->delete();

        // delete ledger and ledger details
        $ledgers = Ledger::where(function ($query) use ($purchase) {
            $query->where('invoice_type', 'purchase')
                  ->orWhere('invoice_type', 'purchase payment')
                  ->orWhere('invoice_type', 'Advance Deduct');
        })->where('invoice_no', $purchase->invoice_number)->get();

        foreach ($ledgers as $ledger) {
            // Delete ledger details first
            $ledger->details()->delete();
            // Then delete the ledger
            $ledger->delete();
        }

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
        // store purchase return
        $purchase = $this->purchaseReturn->create([
            'supplier_id'     => $request->supplier_id,
            'warehouse_id'    => $request->warehouse_id,
            'created_by'      => auth()->user()->id,
            'purchase_id'     => $request->purchase_id,
            'return_type_id'  => $request->return_type_id,
            'return_date'     => Carbon::createFromFormat('d-m-Y', $request->return_date),
            'note'            => $request->note,
            'payment_method'  => $request->payment_type,
            'received_amount' => $request->received_amount ?? 0,
            'return_amount'   => $request->invoice_amount,
            'shipping_cost'   => $request->shipping_cost,
            'invoice'         => $this->returnInvoice($request->return_date),
        ]);

        // store purchase return details

        foreach ($request->product_id as $index => $val) {
            $purchase->purchaseDetails()->create([
                'product_id'  => $val,
                'purchase_id' => $request->purchase_id,
                'quantity'    => $request->return_quantity[$index],
                'total'       => $request->return_subtotal[$index],
            ]);

            // update product stock
            $prod        = Product::find($val);
            $prod->stock = $prod->stock - $request->return_quantity[$index];
            $prod->save();

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
        // amount = received back from supplier (negative = money coming back)
        // total_amount = returned goods value (negative = reduces purchases)
        // due_amount = net balance impact = -(return_amount - received_amount)
        $returnDue = $request->invoice_amount - ($request->received_amount ?? 0);
        $ledger = $this->purchaseReturnLedger(
            $request,
            $purchase->id,
            -($request->received_amount ?? 0),
            'purchase return',
            0,
            -$returnDue,
            null,
            -$request->invoice_amount,
            $purchase->invoice
        );

        // Only create payment if received_amount > 0
        if (($request->received_amount ?? 0)) {
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
                'amount'             => ($request->received_amount ?? 0),
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

        $return->update([
            'supplier_id'     => $request->supplier_id,
            'warehouse_id'    => $request->warehouse_id,
            'return_type_id'  => $request->return_type_id,
            'return_date'     => Carbon::createFromFormat('d-m-Y', $request->return_date),
            'note'            => $request->note,
            'payment_method'  => $request->payment_type,
            'received_amount' => $request->received_amount ?? 0,
            'return_amount'   => $request->invoice_amount,
            'shipping_cost'   => $request->shipping_cost,
            'invoice'         => $newInvoice,
        ]);

        // restore product stock from old return
        foreach ($return->purchaseDetails as $purchaseDetail) {
            $product = Product::find($purchaseDetail->product_id);
            if ($product) {
                $product->stock += $purchaseDetail->quantity;
                $product->save();
            }
        }

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

        // create new purchase return details
        foreach ($request->product_id as $index => $val) {
            $return->purchaseDetails()->create([
                'product_id'  => $val,
                'purchase_id' => $request->purchase_id,
                'quantity'    => $request->return_quantity[$index],
                'total'       => $request->return_subtotal[$index],
            ]);

            // update product stock with new return quantities
            $prod        = Product::find($val);
            $prod->stock = $prod->stock - $request->return_quantity[$index];
            $prod->save();

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

        // Always create ledger entry for purchase return
        // amount = received back from supplier (negative = money coming back)
        // total_amount = returned goods value (negative = reduces purchases)
        // due_amount = net balance impact = -(return_amount - received_amount)
        $returnDue = $request->invoice_amount - ($request->received_amount ?? 0);
        $ledger = $this->purchaseReturnLedger(
            $request,
            $return->id,
            -($request->received_amount ?? 0),
            'purchase return',
            0,
            -$returnDue,
            null,
            -$request->invoice_amount,
            $return->invoice
        );

        // Only create payment if received_amount > 0
        if (($request->received_amount ?? 0)) {
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
                'amount'             => ($request->received_amount ?? 0),
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

        // restore product stock
        foreach ($return->purchaseDetails as $purchaseDetail) {
            $product = Product::find($purchaseDetail->product_id);
            if ($product) {
                $product->stock += $purchaseDetail->quantity;
                $product->save();
            }
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
