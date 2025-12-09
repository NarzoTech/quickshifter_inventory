<?php
namespace Modules\Expense\app\Services;

use App\Models\Ledger;
use Illuminate\Http\Request;
use Modules\Accounts\app\Models\Account;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseSupplierPayment;

class ExpenseService
{
    public function __construct(private Expense $expense, private Account $account)
    {}

    public function all()
    {
        return $this->expense;
    }

    public function find($id)
    {
        return $this->expense->find($id);
    }

    public function store(Request $request)
    {
        if ($request->payment_type == 'cash' || $request->payment_type == 'advance') {
            $account = $this->account->where('account_type', 'cash')->first();
        } else {
            $account = $this->account->find($request->account_id);
        }

        $amount = $request->amount;
        $paidAmount = $request->paid_amount ?? $amount;
        $dueAmount = $amount - $paidAmount;

        // If no supplier, paid amount = full amount (immediate payment)
        if (!$request->expense_supplier_id) {
            $paidAmount = $amount;
            $dueAmount = 0;
        }

        // Store the expense
        $expense = $this->expense->create([
            'invoice'             => $this->genExpenseInvoiceNumber(),
            'date'                => now()->parse($request->date),
            'amount'              => $amount,
            'paid_amount'         => $paidAmount,
            'due_amount'          => $dueAmount,
            'account_id'          => $account->id,
            'payment_type'        => $request->payment_type,
            'note'                => $request->note,
            'memo'                => $request->memo,
            'expense_type_id'     => $request->expense_type_id,
            'sub_expense_type_id' => $request->sub_expense_type_id,
            'expense_supplier_id' => $request->expense_supplier_id,
            'created_by'          => auth('admin')->id(),
        ]);

        // If supplier expense with payment, create payment record
        if ($request->expense_supplier_id && $paidAmount > 0) {
            // Create ledger entry
            $ledger = new Ledger();
            $ledger->expense_supplier_id = $request->expense_supplier_id;
            $ledger->amount = $paidAmount;
            $ledger->invoice_type = 'Expense';
            $ledger->is_paid = 1;
            $ledger->invoice_no = 'EXP-' . $expense->id;
            $ledger->note = $request->note;
            $ledger->due_amount = $dueAmount;
            $ledger->total_amount = $amount;
            $ledger->date = now()->parse($request->date);
            $ledger->created_by = auth('admin')->user()->id;
            $ledger->save();

            $ledger->invoice_url = route('admin.expense.index');
            $ledger->save();

            // Create payment record
            ExpenseSupplierPayment::create([
                'expense_id' => $expense->id,
                'expense_supplier_id' => $request->expense_supplier_id,
                'account_id' => $account->id,
                'payment_type' => 'expense',
                'is_paid' => 1,
                'amount' => $paidAmount,
                'payment_date' => now()->parse($request->date),
                'note' => $request->note,
                'invoice' => $this->genInvoiceNumber(),
                'ledger_id' => $ledger->id,
                'created_by' => auth('admin')->user()->id,
            ]);

            // Create ledger details
            $ledger->details()->create([
                'invoice' => 'EXP-' . $expense->id,
                'amount' => $paidAmount,
            ]);
        }

        return $expense;
    }

    public function update(Request $request, $id)
    {
        if ($request->payment_type == 'cash' || $request->payment_type == 'advance') {
            $account = $this->account->where('account_type', 'cash')->first();
        } else {
            $account = $this->account->find($request->account_id);
        }

        $expense = $this->expense->find($id);
        $oldPaidAmount = $expense->paid_amount;
        $oldSupplierId = $expense->expense_supplier_id;

        $amount = $request->amount;
        $paidAmount = $request->paid_amount ?? $amount;
        $dueAmount = $amount - $paidAmount;

        // If no supplier, paid amount = full amount
        if (!$request->expense_supplier_id) {
            $paidAmount = $amount;
            $dueAmount = 0;
        }

        // Handle payment record updates for supplier expenses
        if ($oldSupplierId && $oldPaidAmount > 0) {
            // Delete old payment records
            $oldPayments = ExpenseSupplierPayment::where('expense_id', $id)
                ->where('payment_type', 'expense')
                ->get();

            foreach ($oldPayments as $payment) {
                if ($payment->ledger) {
                    $payment->ledger->details()->delete();
                    $payment->ledger->delete();
                }
                $payment->delete();
            }
        }

        $expense->update([
            'date'                => now()->parse($request->date),
            'amount'              => $amount,
            'paid_amount'         => $paidAmount,
            'due_amount'          => $dueAmount,
            'note'                => $request->note,
            'memo'                => $request->memo,
            'updated_by'          => auth('admin')->user()->id,
            'account_id'          => $account->id,
            'payment_type'        => $request->payment_type,
            'sub_expense_type_id' => $request->sub_expense_type_id,
            'expense_type_id'     => $request->expense_type_id,
            'expense_supplier_id' => $request->expense_supplier_id,
        ]);

        // Create new payment record if supplier expense
        if ($request->expense_supplier_id && $paidAmount > 0) {
            $ledger = new Ledger();
            $ledger->expense_supplier_id = $request->expense_supplier_id;
            $ledger->amount = $paidAmount;
            $ledger->invoice_type = 'Expense';
            $ledger->is_paid = 1;
            $ledger->invoice_no = 'EXP-' . $expense->id;
            $ledger->note = $request->note;
            $ledger->due_amount = $dueAmount;
            $ledger->total_amount = $amount;
            $ledger->date = now()->parse($request->date);
            $ledger->created_by = auth('admin')->user()->id;
            $ledger->save();

            $ledger->invoice_url = route('admin.expense.index');
            $ledger->save();

            ExpenseSupplierPayment::create([
                'expense_id' => $expense->id,
                'expense_supplier_id' => $request->expense_supplier_id,
                'account_id' => $account->id,
                'payment_type' => 'expense',
                'is_paid' => 1,
                'amount' => $paidAmount,
                'payment_date' => now()->parse($request->date),
                'note' => $request->note,
                'invoice' => $this->genInvoiceNumber(),
                'ledger_id' => $ledger->id,
                'created_by' => auth('admin')->user()->id,
            ]);

            $ledger->details()->create([
                'invoice' => 'EXP-' . $expense->id,
                'amount' => $paidAmount,
            ]);
        }

        return $expense;
    }

    public function destroy($id)
    {
        $expense = $this->expense->find($id);

        // Delete associated payments and ledger entries
        $payments = ExpenseSupplierPayment::where('expense_id', $id)->get();
        foreach ($payments as $payment) {
            if ($payment->ledger) {
                $payment->ledger->details()->delete();
                $payment->ledger->delete();
            }
            $payment->delete();
        }

        return $expense->delete();
    }

    public function genInvoiceNumber()
    {
        $number = 001;
        $prefix = 'ESP-';
        $invoice_number = $prefix . $number;

        $payment = ExpenseSupplierPayment::latest()->first();

        if ($payment) {
            $paymentInvoice = $payment->invoice;

            if ($paymentInvoice) {
                $split_invoice = explode('-', $paymentInvoice);
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }

    public function genExpenseInvoiceNumber()
    {
        $number = 001;
        $prefix = 'EXP-';
        $invoice_number = $prefix . $number;

        $expense = Expense::latest()->first();

        if ($expense && $expense->invoice) {
            $split_invoice = explode('-', $expense->invoice);
            if (count($split_invoice) > 1) {
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }
}
