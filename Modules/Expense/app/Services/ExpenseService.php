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
        $amount = $request->amount;

        // Calculate total paid from multiple payments
        $paymentTypes = $request->payment_type ?? [];
        $accountIds = $request->account_id ?? [];
        $payingAmounts = $request->paying_amount ?? [];

        $paidAmount = 0;
        foreach ($payingAmounts as $amt) {
            $paidAmount += floatval($amt);
        }

        // If no supplier, paid amount = full amount (immediate payment)
        if (!$request->expense_supplier_id) {
            $paidAmount = $amount;
        }

        $dueAmount = $amount - $paidAmount;

        // Get the first payment account for the expense record
        $firstPaymentType = $paymentTypes[0] ?? 'cash';
        $firstAccountId = $accountIds[0] ?? null;

        if ($firstPaymentType == 'cash' || $firstPaymentType == 'advance') {
            $account = $this->account->where('account_type', 'cash')->first();
        } else {
            $account = $this->account->find($firstAccountId);
        }

        // Handle document upload
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = file_upload($request->file('document'), 'uploads/expenses/documents/');
        }

        // Store the expense
        $expense = $this->expense->create([
            'invoice'             => $this->genExpenseInvoiceNumber(),
            'date'                => now()->parse($request->date),
            'amount'              => $amount,
            'paid_amount'         => $paidAmount,
            'due_amount'          => $dueAmount,
            'account_id'          => $account ? $account->id : null,
            'payment_type'        => $firstPaymentType,
            'note'                => $request->note,
            'memo'                => $request->memo,
            'document'            => $documentPath,
            'expense_type_id'     => $request->expense_type_id,
            'sub_expense_type_id' => $request->sub_expense_type_id,
            'expense_supplier_id' => $request->expense_supplier_id,
            'created_by'          => auth('admin')->id(),
        ]);

        // Create payment records for each payment
        if ($request->expense_supplier_id && $paidAmount > 0) {
            // Create ledger entry for total payment
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

            // Create payment record for each payment method
            foreach ($paymentTypes as $index => $paymentType) {
                $paymentAmount = floatval($payingAmounts[$index] ?? 0);
                if ($paymentAmount <= 0) continue;

                $paymentAccountId = $accountIds[$index] ?? null;

                if ($paymentType == 'cash' || $paymentType == 'advance') {
                    $paymentAccount = $this->account->where('account_type', 'cash')->first();
                    $paymentAccountId = $paymentAccount ? $paymentAccount->id : null;
                }

                ExpenseSupplierPayment::create([
                    'expense_id' => $expense->id,
                    'expense_supplier_id' => $request->expense_supplier_id,
                    'account_id' => $paymentAccountId,
                    'payment_type' => 'expense',
                    'is_paid' => 1,
                    'amount' => $paymentAmount,
                    'payment_date' => now()->parse($request->date),
                    'note' => $request->note,
                    'invoice' => $this->genInvoiceNumber(),
                    'ledger_id' => $ledger->id,
                    'created_by' => auth('admin')->user()->id,
                ]);
            }

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

        // Handle document upload
        $documentPath = $expense->document;
        if ($request->hasFile('document')) {
            $documentPath = file_upload($request->file('document'), 'uploads/expenses/documents/', $expense->document);
        }

        $expense->update([
            'date'                => now()->parse($request->date),
            'amount'              => $amount,
            'paid_amount'         => $paidAmount,
            'due_amount'          => $dueAmount,
            'note'                => $request->note,
            'memo'                => $request->memo,
            'document'            => $documentPath,
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
