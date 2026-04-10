<?php

namespace Modules\Accounts\app\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Accounts\app\Models\Account;
use Modules\Expense\app\Models\Expense;

class AccountsService
{
    public function __construct(private Account $account) {}
    public function all()
    {
        return $this->account->with('bank', 'payments');
    }

    public function find(int $id): Account
    {
        return $this->account->findOrFail($id);
    }
    public function create(array $data): Account
    {
        $account = $this->account->create($data);
        $this->cacheClear();
        return $account;
    }

    public function update(Account $account, array $data): Account
    {
        $account->update($data);
        $this->cacheClear();
        return $account;
    }

    public function delete(Account $account): bool
    {
        $this->cacheClear();
        return $account->delete();
    }

    public function getOpeningBalance($fromDate)
    {
        $totalAccounts = $this->account->all();
        $accountBalance = 0;
        $totalAccounts->map(function ($account) use (&$accountBalance, $fromDate) {
            $accountBalance += $account->getOpeningBalance($fromDate);
        });

        // Include unassigned expenses (null account_id) that are not captured by any account
        $unassignedExpenses = Expense::whereNull('expense_supplier_id')
            ->whereDoesntHave('payments')
            ->whereNull('account_id')
            ->where('date', '<', $fromDate)
            ->sum('paid_amount');

        $accountBalance -= $unassignedExpenses;

        return $accountBalance;
    }

    private function cacheClear()
    {
        Cache::forget('accounts');
    }

    public function accountBalance($fromDate, $toDate)
    {

        $totalAccounts = $this->account->all();
        $accountBalance = 0;
        $totalAccounts->map(function ($account) use (&$accountBalance, $fromDate, $toDate) {
            $accountBalance += $account->getBalanceBetween($fromDate, $toDate);
        });

        $unassignedQuery = Expense::whereNull('expense_supplier_id')
            ->whereDoesntHave('payments')
            ->whereNull('account_id');

        if ($fromDate && $toDate) {
            $unassignedQuery->whereBetween('date', [$fromDate, $toDate]);
        }

        $accountBalance -= $unassignedQuery->sum('paid_amount');

        return $accountBalance;
    }
}
