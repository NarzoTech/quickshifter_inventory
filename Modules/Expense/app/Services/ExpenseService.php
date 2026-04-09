<?php
namespace Modules\Expense\app\Services;

use App\Models\Ledger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $amount = round((float) $request->amount, 2);
        if ($amount <= 0) {
            throw new \Exception('Expense amount must be greater than zero.');
        }

        DB::beginTransaction();
        try {

        // Calculate total paid from multiple payments
        $paymentTypes = $request->payment_type ?? [];
        $accountIds = $request->account_id ?? [];
        $payingAmounts = $request->paying_amount ?? [];

        $paidAmount = 0;
        foreach ($payingAmounts as $amt) {
            $paidAmount += floatval($amt);
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

        // Fallback to cash account if no account resolved
        if (!$account) {
            $account = $this->account->where('account_type', 'cash')->first();
        }

        // Handle document upload
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = file_upload($request->file('document'), 'uploads/expenses/documents/');
        }

        // Store the expense
        $expense = $this->expense->create([
            'invoice'             => $this->genExpenseInvoiceNumber($request->date),
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

        // Create payment records for each payment (for all expenses, not just supplier expenses)
        $ledgerId = null;

        // Create ledger entry only for supplier expenses
        if ($request->expense_supplier_id && $paidAmount > 0) {
            $ledger = new Ledger();
            $ledger->expense_supplier_id = $request->expense_supplier_id;
            $ledger->amount = $paidAmount;
            $ledger->invoice_type = 'Expense';
            $ledger->is_paid = 1;
            $ledger->invoice_no = $expense->invoice;
            $ledger->note = $request->note;
            $ledger->due_amount = $dueAmount;
            $ledger->total_amount = $amount;
            $ledger->date = now()->parse($request->date);
            $ledger->created_by = auth('admin')->user()->id;
            $ledger->save();

            $ledger->invoice_url = route('admin.expense.index');
            $ledger->save();
            $ledgerId = $ledger->id;

            // Create ledger details
            $ledger->details()->create([
                'invoice' => $expense->invoice,
                'amount' => $paidAmount,
            ]);
        }

        // Create payment record for each payment method (always, for proper account tracking)
        // Use 'direct_expense' for non-supplier expenses, 'expense' for supplier expenses
        $paymentRecordType = $request->expense_supplier_id ? 'expense' : 'direct_expense';

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
                'payment_type' => $paymentRecordType,
                'is_paid' => 1,
                'amount' => $paymentAmount,
                'payment_date' => now()->parse($request->date),
                'note' => $request->note,
                'invoice' => generateInvoiceNumber(ExpenseSupplierPayment::class, 'invoice', 'ESP', [], $request->date),
                'ledger_id' => $ledgerId,
                'created_by' => auth('admin')->user()->id,
            ]);
        }

        DB::commit();
        return $expense;

        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $expense = $this->expense->find($id);

            // Regenerate invoice if date changed
            $oldInvoice = $expense->invoice;
            $oldDateStr = $expense->date ? Carbon::parse($expense->date)->format('ymd') : '';
            $newDateStr = Carbon::createFromFormat('d-m-Y', $request->date)->format('ymd');
            if ($oldDateStr !== $newDateStr) {
                $expense->invoice = $this->genExpenseInvoiceNumber($request->date);

                // Update invoice references in existing due payment ledger details
                \App\Models\LedgerDetails::where('invoice', $oldInvoice)
                    ->update(['invoice' => $expense->invoice]);
            }

            // Delete old payment records
            $oldPayments = ExpenseSupplierPayment::where('expense_id', $id)
                ->whereIn('payment_type', ['expense', 'direct_expense'])
                ->get();

            foreach ($oldPayments as $payment) {
                if ($payment->ledger_id && $payment->ledger->exists) {
                    $payment->ledger->details()->delete();
                    $payment->ledger->delete();
                }
                $payment->delete();
            }

            $amount = $request->amount;

            // Calculate total paid from multiple payments
            $paymentTypes = $request->payment_type ?? [];
            $accountIds = $request->account_id ?? [];
            $payingAmounts = $request->paying_amount ?? [];

            $paidAmount = 0;
            foreach ($payingAmounts as $amt) {
                $paidAmount += floatval($amt);
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

            // Fallback to cash account if no account resolved
            if (!$account) {
                $account = $this->account->where('account_type', 'cash')->first();
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
                'account_id'          => $account ? $account->id : null,
                'payment_type'        => $firstPaymentType,
                'sub_expense_type_id' => $request->sub_expense_type_id,
                'expense_type_id'     => $request->expense_type_id,
                'expense_supplier_id' => $request->expense_supplier_id,
            ]);

            // Create ledger entry only for supplier expenses
            $ledgerId = null;
            if ($request->expense_supplier_id && $paidAmount > 0) {
                $ledger = new Ledger();
                $ledger->expense_supplier_id = $request->expense_supplier_id;
                $ledger->amount = $paidAmount;
                $ledger->invoice_type = 'Expense';
                $ledger->is_paid = 1;
                $ledger->invoice_no = $expense->invoice;
                $ledger->note = $request->note;
                $ledger->due_amount = $dueAmount;
                $ledger->total_amount = $amount;
                $ledger->date = now()->parse($request->date);
                $ledger->created_by = auth('admin')->user()->id;
                $ledger->save();

                $ledger->invoice_url = route('admin.expense.index');
                $ledger->save();
                $ledgerId = $ledger->id;

                // Create ledger details
                $ledger->details()->create([
                    'invoice' => $expense->invoice,
                    'amount' => $paidAmount,
                ]);
            }

            // Create payment record for each payment method
            // Use 'direct_expense' for non-supplier expenses, 'expense' for supplier expenses
            $paymentRecordType = $request->expense_supplier_id ? 'expense' : 'direct_expense';

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
                    'payment_type' => $paymentRecordType,
                    'is_paid' => 1,
                    'amount' => $paymentAmount,
                    'payment_date' => now()->parse($request->date),
                    'note' => $request->note,
                    'invoice' => generateInvoiceNumber(ExpenseSupplierPayment::class, 'invoice', 'ESP', [], $request->date),
                    'ledger_id' => $ledgerId,
                    'created_by' => auth('admin')->user()->id,
                ]);
            }

            DB::commit();
            return $expense;
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            throw $ex;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $expense = $this->expense->find($id);

            // Delete associated payments and ledger entries
            $payments = ExpenseSupplierPayment::where('expense_id', $id)->get();
            foreach ($payments as $payment) {
                if ($payment->ledger_id && $payment->ledger->exists) {
                    $payment->ledger->details()->delete();
                    $payment->ledger->delete();
                }
                $payment->delete();
            }

            // Also clean up any orphaned ledger entries linked via invoice
            $orphanedLedgers = Ledger::where('invoice_no', $expense->invoice)
                ->where('invoice_type', 'Expense')
                ->get();
            foreach ($orphanedLedgers as $ledger) {
                $ledger->details()->delete();
                $ledger->delete();
            }

            $result = $expense->delete();
            DB::commit();
            return $result;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function duePaySingle(Request $request, $id)
    {
        $expense = $this->expense->findOrFail($id);

        $payAmount = round((float) $request->paying_amount, 2);
        if ($payAmount <= 0) {
            throw new \Exception('Payment amount must be greater than zero.');
        }

        $rawDue = (float) DB::table('expenses')->where('id', $id)->value('due_amount');
        if ($payAmount > $rawDue + 0.01) {
            throw new \Exception(
                "Payment amount (" . number_format($payAmount, 2) . ") exceeds due amount ("
                . number_format($rawDue, 2) . ") for expense: {$expense->invoice}"
            );
        }
        $payAmount = min($payAmount, $rawDue);

        DB::beginTransaction();
        try {
            // Resolve account
            $accountInput = $request->account_id;
            if ($accountInput == 'cash' || $accountInput == 'advance') {
                $account = $this->account->where('account_type', $accountInput)->first();
            } else {
                $account = $this->account->find($accountInput);
            }

            if (!$account) {
                throw new \Exception('Invalid account selected.');
            }

            // Create Ledger entry for supplier expenses
            $ledgerId = null;
            if ($expense->expense_supplier_id) {
                $ledger = new \App\Models\Ledger();
                $ledger->expense_supplier_id = $expense->expense_supplier_id;
                $ledger->amount = $payAmount;
                $ledger->invoice_type = 'Expense Due Payment';
                $ledger->is_paid = 1;
                $ledger->invoice_no = generateInvoiceNumber(\App\Models\Ledger::class, 'invoice_no', 'ESPL', ['invoice_type' => 'Expense Due Payment'], $request->payment_date);
                $ledger->note = $request->note;
                $ledger->due_amount = -$payAmount;
                $ledger->total_amount = 0;
                $ledger->date = now()->parse($request->payment_date);
                $ledger->created_by = auth('admin')->user()->id;
                $ledger->save();

                $ledger->invoice_url = route('admin.expense-suppliers.ledger-details', $ledger->id);
                $ledger->save();
                $ledgerId = $ledger->id;

                $ledger->details()->create([
                    'invoice' => $expense->invoice,
                    'amount' => $payAmount,
                ]);
            }

            // Update expense paid/due amounts
            $rawPaid = (float) DB::table('expenses')->where('id', $id)->value('paid_amount');
            DB::table('expenses')->where('id', $id)->update([
                'paid_amount' => round($rawPaid + $payAmount, 2),
                'due_amount'  => round(max(0, $rawDue - $payAmount), 2),
            ]);

            // Create payment record (goes to cashflow as 'due_pay')
            ExpenseSupplierPayment::create([
                'expense_id' => $expense->id,
                'expense_supplier_id' => $expense->expense_supplier_id,
                'account_id' => $account->id,
                'payment_type' => 'due_pay',
                'is_paid' => 1,
                'amount' => $payAmount,
                'payment_date' => now()->parse($request->payment_date),
                'note' => $request->note,
                'invoice' => generateInvoiceNumber(ExpenseSupplierPayment::class, 'invoice', 'ESP', [], $request->payment_date),
                'ledger_id' => $ledgerId,
                'created_by' => auth('admin')->user()->id,
            ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function genInvoiceNumber($date = null)
    {
        return generateInvoiceNumber(ExpenseSupplierPayment::class, 'invoice', 'ESP', [], $date);
    }

    public function genExpenseInvoiceNumber($date = null)
    {
        return generateInvoiceNumber(Expense::class, 'invoice', 'EXP', [], $date);
    }
}
