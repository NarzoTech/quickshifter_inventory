<?php

namespace Modules\Supplier\app\Services;

use App\Imports\SuppliersImport;
use App\Models\Ledger;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
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
        $suppliers = $this->supplier->query();
        $suppliers = $suppliers->with(['purchaseReturn', 'purchases' => function ($query) {
            if (request()->from_date || request()->to_date) {
                [$from_date, $to_date] = $this->getDateRangeFromRequest();
                if ($from_date) {
                    $query->where('date', '>=', $from_date);
                }
                if ($to_date) {
                    $query->where('date', '<=', $to_date);
                }
            }
        }, 'payments' => function ($query) {
            $query->where('is_paid', 1);

            [$from_date, $to_date] = $this->getDateRangeFromRequest();

            if ($from_date) {
                $query->where('date', '>=', $from_date);
            }

            if ($to_date) {
                $query->where('date', '<=', $to_date);
            }
        }]);


        if (request()->keyword) {
            $suppliers = $suppliers->where(function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%')
                    ->orWhere('phone', 'like', '%' . request()->keyword . '%')
                    ->orWhere('address', 'like', '%' . request()->keyword . '%')
                    ->orWhere('email', 'like', '%' . request()->keyword . '%');
            });
        }

        if (request()->order_by) {
            $suppliers = $suppliers->orderBy('id', request()->order_by);
        }

        if (request()->from_date && request()->to_date) {

            $suppliers = $suppliers->whereBetween('date', [now()->parse(request()->from_date), now()->parse(request()->to_date)]);
        }




        return $suppliers;
    }
    private function getDateRangeFromRequest()
    {
        $from_date = request()->from_date ? now()->parse(request()->from_date) : null;
        $to_date = request()->to_date ? now()->parse(request()->to_date) : null;

        return [$from_date, $to_date];
    }

    public function find($id)
    {
        $supplier = $this->supplier->with('duePurchase')->find($id);

        return $supplier;
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


        // create Ledger
        $ledger = new Ledger();
        $ledger->supplier_id = $id;
        $ledger->amount = $request->paying_amount;
        $ledger->invoice_type = 'Due Payment';
        $ledger->is_paid = 1;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber();
        $ledger->note = $request->note;
        $ledger->due_amount -= $request->paying_amount;
        $ledger->date = now()->parse($request->payment_date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();

        $ledger->invoice_url = route('admin.suppliers.ledger-details', $ledger->id);
        $ledger->save();

        // create payment
        foreach ($request->invoice_no as $index => $invo) {

            if (isset($request->amount[$index]) && $request->amount[$index] == 0) {
                continue;
            }
            $purchase = Purchase::where('invoice_number', $invo)->first();

            $purchase->paid_amount = $purchase->paid_amount + $request->amount[$index];
            $purchase->due_amount = $purchase->due_amount - $request->amount[$index];
            $purchase->payment_status = $purchase->due_amount == 0 ? 'paid' : 'due';
            $purchase->save();

            // create payment data
            SupplierPayment::create([
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

            // create ledger details
            $ledger->details()->create([
                'invoice' => $invo,
                'amount' => $request->amount[$index],
            ]);
        }
    }

    public function duePayHistory()
    {
        $list  = SupplierPayment::whereNotNull('purchase_id')->where('payment_type', 'due_pay')->get();
        return $list;
    }

    public function dueReceiveDelete($id)
    {
        $payment = SupplierPayment::find($id);

        $payment->purchase->paid_amount = $payment->purchase->paid_amount - $payment->amount;
        $payment->purchase->due_amount = $payment->purchase->due_amount + $payment->amount;
        $payment->purchase->payment_status = $payment->purchase->due_amount == 0 ? 'paid' : 'due';
        $payment->purchase->save();

        $payment->delete();
    }

    public function genInvoiceNumber()
    {
        $number = 001;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $purchase = SupplierPayment::latest()->first();

        if ($purchase) {
            $purchaseInvoice = $purchase->invoice;

            if ($purchaseInvoice) {
                // split the invoice number
                $split_invoice = explode('-', $purchaseInvoice);
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
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

        // create ledger

        $ledger = new Ledger();
        $ledger->supplier_id = $id;
        $ledger->amount = $request->paying_amount ?? $request->refund_amount;
        $ledger->invoice_type = $request->refund_amount == null ? 'Advance Payment' : 'Payment Return';
        $ledger->is_paid = $request->refund_amount != null ? 0 : 1;
        $ledger->is_received = $request->refund_amount != null ? 1 : 0;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber();
        $ledger->note = $request->note;
        if ($request->refund_amount != null) {
            $ledger->due_amount += $request->refund_amount;
        } else {
            $ledger->due_amount -= $request->paying_amount;
        }
        $ledger->date = now()->parse($request->date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();
    }


    public function genLedgerInvoiceNumber()
    {
        $number = 001;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $purchase = Ledger::where('invoice_type', 'Due Payment')->latest()->first();
        if ($purchase) {
            $purchaseInvoice = $purchase->invoice_no;

            if ($purchaseInvoice) {
                // split the invoice number
                $split_invoice = explode('-', $purchaseInvoice);
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }


    public function bulkImport(Request $request)
    {
        $file = $request->file('file');
        Excel::import(new SuppliersImport, $file);
    }
}
