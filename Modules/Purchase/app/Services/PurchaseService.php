<?php

namespace Modules\Purchase\Services;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Purchase\app\Models\Purchase;
use Modules\Purchase\app\Models\PurchaseDetails;
use Modules\Supplier\app\Models\Supplier;
use Modules\Product\app\Models\Product;


class PurchaseService
{

    public function __construct(private Purchase $purchase, private PurchaseDetails $purchaseDetails)
    {
    }

    public function all()
    {
        return $this->purchase->with('supplier', 'warehouse')->latest();
    }
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $purchase = new Purchase();
            $purchase->supplier_id = $request->supplier_id;
            $purchase->warehouse_id = $request->warehouse_id;
            $purchase->invoice_number = $request->invoice_number;
            $purchase->reference_no = $request->reference_no;
            $purchase->purchase_date = $request->purchase_date;
            $purchase->items = $request->items;
            $purchase->total_amount = $request->total_amount;
            $purchase->paid_amount = $request->paid_amount;
            $purchase->due_amount = $request->due_amount;
            $purchase->payment_status = $request->payment_status;
            $purchase->payment_type = $request->payment_type;
            $purchase->note = $request->note;
            $purchase->status = $request->status;
            $purchase->created_by = Auth::id();
            $purchase->save();

            foreach ($request->items as $item) {
                $purchaseDetails = new PurchaseDetails();
                $purchaseDetails->purchase_id = $purchase->id;
                $purchaseDetails->product_id = $item['product_id'];
                $purchaseDetails->quantity = $item['quantity'];
                $purchaseDetails->purchase_price = $item['purchase_price'];
                $purchaseDetails->sub_total = $item['sub_total'];
                $purchaseDetails->profit = $item['profit'];
                $purchaseDetails->sale_price = $item['sale_price'];
                $purchaseDetails->discount = $item['discount'];
                $purchaseDetails->tax = $item['tax'];
                $purchaseDetails->created_by = Auth::id();
                $purchaseDetails->save();

                $product = Product::find($item['product_id']);
                $product->purchase_price = $item['purchase_price'];
                $product->sale_price = $item['sale_price'];
                $product->save();
            }

            DB::commit();
            return $purchase;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $purchase = $this->purchase->find($id);
            $purchase->supplier_id = $request->supplier_id;
            $purchase->warehouse_id = $request->warehouse_id;
            $purchase->invoice_number = $request->invoice_number;
            $purchase->reference_no = $request->reference_no;
            $purchase->purchase_date = $request->purchase_date;
            $purchase->items = $request->items;
            $purchase->total_amount = $request->total_amount;
            $purchase->paid_amount = $request->paid_amount;
            $purchase->due_amount = $request->due_amount;
            $purchase->payment_status = $request->payment_status;
            $purchase->payment_type = $request->payment_type;
            $purchase->note = $request->note;
            $purchase->status = $request->status;
            $purchase->updated_by = Auth::id();
            $purchase->save();

            PurchaseDetails::where('purchase_id', $id)->delete();

            foreach ($request->items as $item) {
                $purchaseDetails = new PurchaseDetails();
                $purchaseDetails->purchase_id = $purchase->id;
                $purchaseDetails->product_id = $item['product_id'];
                $purchaseDetails->quantity = $item['quantity'];
                $purchaseDetails->purchase_price = $item['purchase_price'];
                $purchaseDetails->sub_total = $item['sub_total'];
                $purchaseDetails->profit = $item['profit'];
                $purchaseDetails->sale_price = $item['sale_price'];
                $purchaseDetails->discount = $item['discount'];
                $purchaseDetails->tax = $item['tax'];
                $purchaseDetails->created_by = Auth::id();
                $purchaseDetails->save();

                $product = Product::find($item['product_id']);
                $product->purchase_price = $item['purchase_price'];
                $product->sale_price = $item['sale_price'];
                $product->save();
            }

            DB::commit();
            return $purchase;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->purchase->find($id)->delete();
            PurchaseDetails::where('purchase_id', $id)->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function getPurchase($id)
    {
        return $this->purchase->with('supplier', 'warehouse', 'purchaseDetails.product')->find($id);
    }

    public function getPurchaseDetails($id)
    {
        return PurchaseDetails::with('product')->where('purchase_id', $id)->get();
    }

    public function getPurchaseList()
    {
        return $this->purchase->with('supplier', 'warehouse')->latest()->get();
    }

    public function getSupplierList()
    {
        return Supplier::latest()->get();
    }
}