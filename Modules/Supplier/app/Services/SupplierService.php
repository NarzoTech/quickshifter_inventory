<?php

namespace Modules\Supplier\app\Services;

use App\Models\Payment;
use Illuminate\Http\Request;
use Modules\Accounts\app\Models\Account;
use Modules\Purchase\app\Models\Purchase;
use Modules\Supplier\app\Models\Supplier;
use Modules\Supplier\app\Models\SupplierPayment;

class SupplierService
{
    public function __construct(private Supplier $supplier) {}

    public function all()
    {
        return $this->supplier->where('status', 1)->with('purchaseReturn');
    }

    public function allSupplier()
    {
        return $this->supplier->with('purchaseReturn');
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

        if ($account == 'cash' || $account == 'advance') {
            $account = Account::where('account_type', $account)?->first();
        } else {
            $account = Account::find($account);
        }


        foreach ($request->invoice_no as $index => $invo) {
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
                'is_paid' => 1,
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

    public function genInvoiceNumber()
    {
        $number = 001;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $purchase = SupplierPayment::latest()->first();
        if ($purchase) {
            $purchaseInvoice = $purchase->invoice;

            // split the invoice number
            $split_invoice = explode('-', $purchaseInvoice);
            $invoice_number = (int) $split_invoice[1] + 1;
            $invoice_number = $prefix . $invoice_number;
        }

        return $invoice_number;
    }


    public function advancePay(Request $request, $id)
    {
        $account = $request->account_id;

        if ($account == 'cash' || $account == 'advance') {
            $account = Account::where('account_type', $account)?->first();
        } else {
            $account = Account::find($account);
        }

        // create payment data
        SupplierPayment::create([
            'supplier_id' => $id,
            'account_id' => $account->id,
            'payment_type' => $request->refund_amount != null ? 'advance_refund' : 'advance_pay',
            'is_paid' => $request->refund_amount != null ? 0 : 1,
            'is_received' => $request->refund_amount != null ? 1 : 0,
            'amount' => $request->refund_amount != null ? $request->refund_amount : $request->paying_amount,
            'account_type' => accountList()[$account->account_type],
            'note' => $request->note,
            'created_by' => auth('admin')->user()->id,
            'payment_date' => now()->parse($request->date),
            'invoice' => $this->genInvoiceNumber()
        ]);
    }
}
