<?php

namespace Modules\Accounts\app\Services;

use Modules\Accounts\app\Models\Account;

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
        return $this->account->create($data);
    }

    public function update(Account $account, array $data): Account
    {
        $account->update($data);

        return $account;
    }

    public function delete(Account $account): bool
    {
        return $account->delete();
    }

    public function getOpeningBalance($fromDate)
    {
        $totalAccounts = $this->account->all();
        $accountBalance = 0;
        $totalAccounts->map(function ($account) use (&$accountBalance, $fromDate) {
            $accountBalance += $account->getOpeningBalance($fromDate);
        });

        return $accountBalance;
    }

    public function accountBalance($fromDate, $toDate)
    {

        $totalAccounts = $this->account->all();
        $accountBalance = 0;
        $totalAccounts->map(function ($account) use (&$accountBalance, $fromDate, $toDate) {
            $accountBalance += $account->getBalanceBetween($fromDate, $toDate);
        });

        return $accountBalance;
    }
}
