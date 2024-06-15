<?php

namespace Modules\Supplier\app\Services;

use App\Models\Payment;
use Illuminate\Http\Request;
use Modules\Accounts\app\Models\Account;
use Modules\Purchase\app\Models\Purchase;
use Modules\Supplier\app\Models\Supplier;

class SupplierService
{
    public function __construct(private Supplier $supplier)
    {
    }

    public function all()
    {
        return $this->supplier;
    }

    public function find($id)
    {
        return $this->supplier->find($id);
    }

    public function storeSupplier(Request $request)
    {
        $data = $request->except('_token');
        $data['created_by'] = auth()->id();
        $data['date'] = now()->parse($request->date);
        return $this->supplier->create($data);
    }

    public function updateSupplier(Request $request, $id)
    {
        $data = $request->except(['_token', '_method']);
        $data['updated_by'] = auth()->id();
        $data['date'] = now()->parse($request->date);
        return $this->supplier->where('id', $id)->update($data);
    }

    public function deleteSupplier($id)
    {
        return $this->supplier->where('id', $id)->delete();
    }

    public function duePay(Request $request, $id)
    {
        $supplier = $this->supplier->find($id);

        $supplier->balance = $supplier->balance - $request->paying_amount;
        $supplier->save();

        // account information

        $account = $request->account_id;

        if($account == 'cash' || $account == 'advance'){
            $account = Account::where('account_type', $account)?->first();
        }else{
            $account = Account::find($account);
        }
        

        foreach ($request->invoice_no as $index=>$invo) {
            $purchase = Purchase::where('invoice_number', $invo)->first();

            $purchase->paid_amount = $purchase->paid_amount + $request->amount[$index];
            $purchase->due_amount = $purchase->due_amount - $request->amount[$index];
            $purchase->payment_status = $purchase->due_amount == 0 ? 'paid' : 'due';
            $purchase->save();

            // create payment data
            Payment::create([
                'purchase_id' => $purchase->id,
                'supplier_id' => $id,
                'account_id' => $account->id,
                'payment_type' => 'due_pay',
                'amount' => $request->amount[$index],
                'payment_date' => now()->parse($request->payment_date),
                'note' => $request->note,
                'created_by' => auth('admin')->user()->id,
            ]);
        }
    }

    public function duePayHistory()
    {
        $list  = Payment::whereNotNull('purchase_id')->where('payment_type', 'due_pay')->get();
        return $list;
    }
}
