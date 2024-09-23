<?php

namespace Modules\Expense\app\Services;

use App\Models\Payment;
use Illuminate\Http\Request;
use Modules\Accounts\app\Models\Account;
use Modules\Expense\app\Models\Expense;

class ExpenseService
{
    public function __construct(private Expense $expense, private Account $account) {}

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

        // store the expense

        $expense = $this->expense->create([
            'date' => now()->parse($request->date),
            'amount' => $request->amount,
            'account_id' => $account->id,
            'payment_type' => $request->payment_type,
            'note' => $request->note,
            'expense_type_id' => $request->expense_type_id,
            'created_by' => auth('admin')->id(),
        ]);

        // create the transaction
        Payment::create([
            'expense_id' => $expense->id,
            'account_id' => $account->id,
            'payment_type' => 'expense',
            'amount' => $request->amount,
            'payment_date' => now()->parse($request->purchase_date),
            'is_paid' => 1,
            'note' => $request->note,
            'created_by' => auth('admin')->user()->id,
        ]);

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
        $expense->update([
            'date' => now()->parse($request->date),
            'amount' => $request->amount,
            'note' => $request->note,
            'updated_by' => auth('admin')->user()->id,
            'account_id' => $account->id,
            'payment_type' => $request->payment_type,
            'expense_type_id' => $request->expense_type_id,
        ]);


        // update payment
        $payment = Payment::where('expense_id', $id)->first();
        $payment->update([
            'account_id' => $account->id,
            'payment_type' => 'expense',
            'amount' => $request->amount,
            'payment_date' => now()->parse($request->purchase_date),
            'is_paid' => 1,
            'note' => $request->note,
            'updated_by' => auth('admin')->user()->id,
        ]);

        return $expense;
    }

    public function destroy($id)
    {
        $expense = $this->expense->find($id);
        // delete payment and from trash
        $expense->payments()->withTrashed()->forceDelete();



        // delete expense a
        return $expense->delete();
    }
}
