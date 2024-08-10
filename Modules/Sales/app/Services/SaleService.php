<?php

namespace Modules\Sales\app\Services;

use App\Models\Payment;
use Illuminate\Http\Request;
use Modules\Accounts\app\Models\Account;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Product\app\Models\Product;
use Modules\Product\app\Models\Variant;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;
use Modules\Service\app\Models\Service;

class SaleService
{
    public function __construct(private Sale $sale) {}
    public function getSales()
    {
        return $this->sale->with('products', 'user', 'services', 'details', 'payment');
    }
    public function createSale(Request $request, $user, $cart): Sale
    {
        $sale = new Sale();
        $sale->user_id = $user != null ?  $user->id : null;
        $sale->customer_id = $request->order_customer_id;
        $sale->warehouse_id = 1;
        $sale->quantity = 1;
        $sale->total_price = $request->sub_total;
        $sale->order_date = now()->parse($request->sale_date);
        $sale->status = 1;
        $sale->payment_status = 1;

        $sale->payment_method = json_encode($request->payment_type);
        $sale->order_discount = $request->discount_amount;
        $sale->total_tax = $request->total_tax ?? 0;
        $sale->grand_total = $request->total_amount;
        $sale->invoice = $this->genInvoiceNumber();

        $sale->paid_amount = array_sum($request->paying_amount);
        $sale->receive_amount = $request->receive_amount;
        $sale->return_amount = $request->return_amount;
        $due = $request->total_amount - array_sum($request->paying_amount);
        $sale->due_amount = $due < 0 ? 0 : $due;
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
            $orderDetails->quantity = $item['qty'];
            $orderDetails->sub_total = $item['sub_total'];
            $orderDetails->attributes = $variant != null ? $item['variant']['attribute'] : null;
            $orderDetails->save();

            // update stock
            $product = Product::where('id', $item['id'])->first();
            if ($product != null && $item['type'] == 'product' && $item['source'] == 1) {
                $product->stock = $product->stock - $item['qty'];
                $product->stock_status = $product->stock <= 0 ? 'out_of_stock' : 'in_stock';
                $product->save();
            }
        }

        $sale->quantity = $totalQty;
        $sale->save();


        // create payments
        foreach ($request->payment_type as $key => $item) {
            $account = Account::where('account_type', $item);
            if ($item == 'cash') {
                $account = $account->first();
            } else {
                $account = $account->where('id', $request->account_id[$key])->first();
            }
            $customerId = $request->order_customer_id;
            $data = [
                'payment_type' => 'sale',
                'sale_id' => $sale->id,
                'is_received' => 1,
                'customer_id' => $request->order_customer_id,
                'account_id' => $account->id,
                'amount' => $request->paying_amount[$key],
                'payment_date' => now(),
                'created_by' => auth()->user()->id,
            ];
            if ($customerId == 'walk-in-customer') {
                $data['customer_id'] = null;
                $data['is_guest'] = 1;
            }
            Payment::create($data);
        }


        // create due
        if ($request->total_due && $user) {
            CustomerDue::create([
                'invoice' => $sale->invoice,
                'due_amount' => $request->total_due,
                'due_date' => $request->due_date,
                'status' => 1,
                'customer_id' => $user->id
            ]);
        }

        return $sale;
    }

    public function updateSale(Request $request, $user, $cart, $id): Sale
    {
        $sale = $this->sale->find($id);


        // update sales
        $sale->user_id = $user != null ?  $user->id : null;
        $sale->customer_id = $request->order_customer_id;
        $sale->warehouse_id = 1;
        $sale->total_price = $request->sub_total;
        $sale->order_date = now()->parse($request->sale_date);
        $sale->status = 1;
        $sale->payment_status = 1;

        $sale->payment_method = json_encode($request->payment_type);
        $sale->order_discount = $request->discount_amount;
        $sale->total_tax = $request->total_tax ?? 0;
        $sale->grand_total = $request->total_amount;
        $sale->paid_amount = array_sum($request->paying_amount);

        $due = $request->total_amount - array_sum($request->paying_amount);
        $sale->due_amount = $due < 0 ? 0 : $due;
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
            $orderDetails->quantity = $item['qty'];
            $orderDetails->sub_total = $item['sub_total'];
            $orderDetails->attributes = $variant != null ? $item['variant']['attribute'] : null;
            $orderDetails->save();

            // update stock
            $product = Product::where('id', $item['id'])->first();
            if ($product != null && $item['type'] == 'product' && $item['source'] == 1) {
                $product->stock = $product->stock - $item['qty'];
                $product->stock_status = $product->stock <= 0 ? 'out_of_stock' : 'in_stock';
                $product->save();
            }
        }

        $sale->quantity = $totalQty;
        $sale->save();


        // create payments
        foreach ($request->payment_type as $key => $item) {
            $account = Account::where('account_type', $item);
            if ($item == 'cash') {
                $account = $account->first();
            } else {
                $account = $account->where('id', $request->account_id[$key])->first();
            }
            $customerId = $request->order_customer_id;
            $data = [
                'payment_type' => 'sale',
                'sale_id' => $sale->id,
                'is_received' => 1,
                'customer_id' => $request->order_customer_id,
                'account_id' => $account->id,
                'amount' => $request->paying_amount[$key],
                'payment_date' => now(),
                'created_by' => auth()->user()->id,
            ];
            if ($customerId == 'walk-in-customer') {
                $data['customer_id'] = null;
                $data['is_guest'] = 1;
            }
            Payment::create($data);
        }


        // create due
        if ($request->total_due && $user) {
            CustomerDue::create([
                'invoice' => $sale->invoice,
                'due_amount' => $request->total_due,
                'due_date' => $request->due_date,
                'status' => 1,
                'customer_id' => $user->id
            ]);
        }
        return $sale;
    }

    public function deleteSale($id): void
    {
        $sale = $this->sale->find($id);

        // delete sales related all info

        // delete payments
        $sale->payments()->delete();

        // delete due
        $sale->customer_due()->delete();

        // delete sale details
        $sale->details()->delete();

        // delete sale
        $sale->delete();
    }

    public function genInvoiceNumber()
    {
        $number = 001;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $sale = $this->sale->latest()->first();
        if ($sale) {
            $saleInvoice = $sale->invoice;

            // split the invoice number
            $split_invoice = explode('-', $saleInvoice);
            $invoice_number = (int) $split_invoice[1] + 1;
            $invoice_number = $prefix . $invoice_number;
        }

        return $invoice_number;
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

            if ($detail->variant_id) {
                $data['variant']['attribute'] =  $attributes;
                $data['variant']['options'] =  $options;
            }
            $cart_contents = session()->get('UPDATE_CART');
            $cart_contents = $cart_contents ? $cart_contents : [];
            session()->put('UPDATE_CART', [...$cart_contents, $data["rowid"] => $data]);
        }
        $cart_contents = session()->get('UPDATE_CART');
        return [$cart_contents, $sale];
    }
}
