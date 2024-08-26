<?php

namespace Modules\Purchase\app\Services;

use App\Models\Ledger;
use App\Models\Payment;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Accounts\app\Models\Account;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Purchase\app\Models\Purchase;
use Modules\Purchase\app\Models\PurchaseDetails;
use Modules\Supplier\app\Models\Supplier;
use Modules\Product\app\Models\Product;
use Modules\Product\app\Services\ProductService;
use Modules\Purchase\app\Models\PurchaseReturn;
use Modules\Purchase\app\Models\PurchaseReturnDetails;
use Modules\Purchase\app\Models\PurchaseReturnType;
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
    ) {}

    public function all()
    {
        return $this->purchase->with('supplier', 'warehouse')->latest();
    }

    public function allReturn()
    {
        return $this->purchaseReturn->with('purchase', 'returnType', 'purchaseDetails')->latest();
    }
    public function store($request)
    {
        $attachment_name = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');
            $attachment_name = time() . '.' . $attachment->getClientOriginalExtension();
            $attachment->move(public_path('uploads/purchase/'), $attachment_name);
        }
        $purchase = new Purchase();
        $paidAmount = $request->total_amount - $request->due_amount;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->warehouse_id = $request->warehouse_id;
        $purchase->invoice_number = $request->invoice_number;
        $purchase->memo_no = $request->memo_no;
        $purchase->reference_no = $request->reference_no;
        $purchase->purchase_date = now()->parse($request->purchase_date);
        $purchase->items = $request->items;
        $purchase->attachment = $attachment_name;
        $purchase->total_amount = $request->total_amount;
        $purchase->paid_amount = $paidAmount;
        $purchase->due_amount = $request->due_amount;
        $purchase->payment_status = $paidAmount == $request->total_amount ? 'paid' : 'due';
        $purchase->payment_type = $request->payment_type;
        $purchase->note = $request->note;
        $purchase->status = $request->status;
        $purchase->created_by = Auth::id();
        $purchase->save();

        $this->updateLedger($request, $purchase->id, $paidAmount, 'purchase');

        foreach ($request->product_id as $index => $id) {
            $purchaseDetails = new PurchaseDetails();
            $purchaseDetails->purchase_id = $purchase->id;
            $purchaseDetails->product_id = $id;
            $purchaseDetails->quantity = $request->quantity[$index];
            $purchaseDetails->purchase_price = $request->unit_price[$index];
            $purchaseDetails->sale_price = $request->selling_price[$index];
            $purchaseDetails->sub_total = $request->total[$index];
            $purchaseDetails->profit = $request->profit[$index];
            $purchaseDetails->created_by = Auth::id();
            $purchaseDetails->save();

            $product = Product::find($id);
            $product->stock += $request->quantity[$index];
            $product->save();


            // create stock
            Stock::create([
                'purchase_id' => $purchase->id,
                'product_id' => $product->id,
                'quantity' => $request->stock[$index],
                'sku' => $product->sku,
                'purchase_price' => $request->unit_price[$index],
                'sale_price' => $request->selling_price[$index],
                'profit' => $request->profit[$index],
                'created_by' => auth('admin')->user()->id,
            ]);
        }


        // create payments
        foreach ($request->payment_type as $key => $item) {
            $account = Account::where('account_type', $item);
            if ($item == 'cash') {
                $account = $account->first();
            } else {
                $account = $account->where('id', $request->account_id[$key])->first();
            }
            $data = [
                'payment_type' => 'purchase',
                'purchase_id' => $purchase->id,
                'is_paid' => 1,
                'supplier_id' => $request->supplier_id,
                'account_id' => $account->id,
                'amount' => $request->paid_amount[$key],
                'payment_date' => now()->parse($request->purchase_date),
                'note' => $request->note,
                'created_by' => auth('admin')->user()->id,
                'account_type' => accountList()[$item],
                'invoice' => $request->invoice_number,
            ];
            SupplierPayment::create($data);
        }

        return $purchase;
    }

    public function update($request, $id)
    {
        $purchase = $this->purchase->find($id);

        $attachment_name = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');
            $attachment_name = file_upload($attachment, oldFile: $purchase->attachment);
        }


        $paidAmount = $request->total_amount - $request->due_amount;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->warehouse_id = $request->warehouse_id;
        $purchase->invoice_number = $request->invoice_number;
        $purchase->memo_no = $request->memo_no;
        $purchase->reference_no = $request->reference_no;
        $purchase->purchase_date = now()->parse($request->purchase_date);
        $purchase->items = $request->items;
        $purchase->attachment = $attachment_name;
        $purchase->total_amount = $request->total_amount;
        $purchase->paid_amount = $paidAmount;
        $purchase->due_amount = $request->due_amount;
        $purchase->payment_status = $request->paid_amount == $request->total_amount ? 'paid' : 'due';
        $purchase->payment_type = $request->payment_type;
        $purchase->note = $request->note;
        $purchase->status = $request->status;
        $purchase->updated_by = Auth::id();
        $purchase->save();


        $this->updateLedger($request, $purchase->id, $paidAmount, 'purchase');

        // restore product stock
        foreach ($purchase->purchaseDetails as $purchaseDetail) {
            $product = Product::find($purchaseDetail->product_id);
            $product->stock -= $purchaseDetail->quantity;
            $product->save();
        }

        // delete old purchase details
        $purchase->purchaseDetails()->delete();
        $purchase->payments()->delete();
        $purchase->stock()->delete();


        // store new purchase details
        foreach ($request->product_id as $index => $id) {
            $purchaseDetails = new PurchaseDetails();
            $purchaseDetails->purchase_id = $purchase->id;
            $purchaseDetails->product_id = $id;
            $purchaseDetails->quantity = $request->quantity[$index];
            $purchaseDetails->purchase_price = $request->unit_price[$index];
            $purchaseDetails->sale_price = $request->selling_price[$index];
            $purchaseDetails->sub_total = $request->total[$index];
            $purchaseDetails->profit = $request->profit[$index];
            $purchaseDetails->created_by = Auth::id();
            $purchaseDetails->save();

            $product = Product::find($id);
            $product->stock += $request->quantity[$index];
            $product->save();


            // create stock
            Stock::create([
                'purchase_id' => $purchase->id,
                'product_id' => $product->id,
                'quantity' => $request->stock[$index],
                'sku' => $product->sku,
                'purchase_price' => $request->unit_price[$index],
                'sale_price' => $request->selling_price[$index],
                'profit' => $request->profit[$index],
                'created_by' => auth('admin')->user()->id,
            ]);
        }

        // create payments
        foreach ($request->payment_type as $key => $item) {
            $account = Account::where('account_type', $item);
            if ($item == 'cash') {
                $account = $account->first();
            } else {
                $account = $account->where('id', $request->account_id[$key])->first();
            }
            $data = [
                'payment_type' => 'purchase',
                'purchase_id' => $purchase->id,
                'is_paid' => 1,
                'supplier_id' => $request->supplier_id,
                'account_id' => $account->id,
                'amount' => $request->paid_amount[$key],
                'payment_date' => now()->parse($request->purchase_date),
                'note' => $request->note,
                'created_by' => auth('admin')->user()->id,
                'invoice' => $request->invoice_number,
                'account_type' => accountList()[$item],
            ];
            SupplierPayment::create($data);
        }

        // update ledger
        // $this->updateLedger($request, $purchase->id, $paidAmount);

        return $purchase;
    }

    public function destroy($id)
    {
        // restore product stock
        foreach ($this->purchase->find($id)->purchaseDetails as $purchaseDetail) {
            $product = Product::find($purchaseDetail->product_id);
            $product->stock -= $purchaseDetail->quantity;
            $product->save();
        }

        PurchaseDetails::where('purchase_id', $id)?->delete();
        Stock::where('purchase_id', $id)?->delete();
        SupplierPayment::where('purchase_id', $id)?->delete();

        // delete ledger
        Ledger::where('invoice_no', $this->purchase->find($id)->invoice_number)->delete();
        $this->purchase->find($id)->delete();
    }

    public function genInvoiceNumber()
    {
        $number = 001;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $purchase = $this->purchase->latest()->first();
        if ($purchase) {
            $purchaseInvoice = $purchase->invoice_number;

            // split the invoice number
            $split_invoice = explode('-', $purchaseInvoice);
            $invoice_number = (int) $split_invoice[1] + 1;
            $invoice_number = $prefix . $invoice_number;
        }

        return $invoice_number;
    }

    public function getPurchase($id)
    {
        return $this->purchase->with('supplier', 'warehouse', 'purchaseDetails.product', 'payments')->find($id);
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
        return Supplier::where('status', 1)->latest()->get();
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
    public function storeReturn(Request $request)
    {
        // store purchase return
        $purchase = $this->purchaseReturn->create([
            'supplier_id' => $request->supplier_id,
            'warehouse_id' => $request->warehouse_id,
            'created_by' => auth()->user()->id,
            'purchase_id' => $request->purchase_id,
            'return_type_id' => $request->return_type_id,
            'return_date' => now()->parse($request->return_date),
            'note' => $request->note,
            'payment_method' => $request->payment_type,
            'received_amount' => $request->received_amount,
            'return_amount' => $request->invoice_amount,
            'shipping_cost' => $request->shipping_cost,
        ]);


        // store purchase return details

        foreach ($request->product_id as $index => $val) {
            $purchase->purchaseDetails()->create([
                'product_id' => $val,
                'purchase_id' => $request->purchase_id,
                'quantity' => $request->return_quantity[$index],
                'total' => $request->return_subtotal[$index],
            ]);


            // update product stock
            $prod = Product::find($val);
            $prod->stock = $prod->stock - $request->return_quantity[$index];
            $prod->save();
        }

        $account = Account::where('account_type', $request->payment_type);
        if ($request->payment_type == 'cash') {
            $account = $account->first();
        } else {
            $account = $account->where('id', $request->account_id)->first();
        }

        if ($request->received_amount) {
            Payment::create([
                'payment_type' => 'purchase_receive',
                'purchase_id' => $request->purchase_id,
                'account_id' => $account->id,
                'amount' => $request->received_amount,
                'payment_date' => now(),
                'created_by' => auth()->user()->id,
            ]);
        }

        if ($request->shipping_cost) {
            Payment::create([
                'payment_type' => 'purchase_cost',
                'purchase_id' => $request->purchase_id,
                'account_id' => $account->id,
                'amount' => $request->shipping_cost,
                'payment_date' => now(),
                'created_by' => auth()->user()->id,
            ]);
        }

        return $purchase;
    }


    public function updateLedger($request, $id, $paidAmount, $type = 'purchase')
    {
        $purchase = $this->purchase->find($id);

        // check if ledger already exist

        // $ledger = Ledger::where('supplier_id', $request->supplier_id)
        //     ->where('invoice_type', 'purchase')
        //     ->where('invoice_no', $purchase->invoice_number)
        //     ->first();
        // if ($ledger) {
        //     // delete ledger
        //     $ledger->delete();
        // }

        $ledger = new Ledger();
        $ledger->supplier_id = $request->supplier_id;
        $ledger->amount = $paidAmount;
        $ledger->invoice_type = $type;
        $ledger->is_paid = 1;
        $ledger->invoice_url = route('admin.purchase.invoice', $purchase->id);
        $ledger->invoice_no = $request->invoice_number;
        $ledger->note = $request->note;
        $ledger->due_amount = $request->due_amount;
        $ledger->date = now()->parse($request->purchase_date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();
    }
}
