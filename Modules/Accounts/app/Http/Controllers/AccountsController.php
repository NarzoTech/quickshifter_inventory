<?php

namespace Modules\Accounts\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Traits\RedirectHelperTrait;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Accounts\app\Http\Requests\AccountRequest;
use Modules\Accounts\app\Models\BalanceTransfer;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Accounts\app\Services\BankService;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Employee\app\Models\EmployeeSalary;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseSupplierPayment;
use Modules\Sales\app\Models\ProductSale;
use Modules\Supplier\app\Models\SupplierPayment;

class AccountsController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private BankService $bankService, private AccountsService $accountsService)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('account.view');
        $accounts = $this->accountsService->all()->paginate(20);
        $accounts->appends(request()->query());
        $bankAccounts = $this->accountsService->all()->where('account_type', 'bank')->with('payments')->get();
        $cashAccount = $this->accountsService->all()->where('account_type', 'cash')->with('payments')->first();
        $mobileAccounts = $this->accountsService->all()->where('account_type', 'mobile_banking')->with('payments')->get();
        $cardAccounts = $this->accountsService->all()->where('account_type', 'card')->with('payments')->get();
        $advanceAccounts = $this->accountsService->all()->where('account_type', 'advance')->with('payments')->get();

        $totalAccounts = $this->accountsService->all()->get();

        $accountBalance = 0;
        $totalAccounts->map(function ($account) use (&$accountBalance) {
            $accountBalance += $account->getBalanceBetween();
        });


        return view('accounts::index', compact('accounts', 'bankAccounts', 'cashAccount', 'mobileAccounts', 'cardAccounts', 'advanceAccounts', 'accountBalance'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('account.create');
        $accounts = $this->bankService->all()->get();
        return view('accounts::create', compact('accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AccountRequest $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('account.create');
        try {
            $this->accountsService->create($request->except('_token'));
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.accounts.create', [], ['messege' => 'Account created successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.accounts.create', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('accounts::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        checkAdminHasPermissionAndThrowException('account.edit');
        $account = $this->accountsService->find($id);
        $accounts = $this->bankService->all()->get();
        return view('accounts::edit', compact('account', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AccountRequest $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('account.edit');
        try {
            $account = $this->accountsService->find($id);
            $this->accountsService->update($account, $request->except('_token'));
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.accounts.edit', ['account' => $id], ['messege' => 'Account updated successfully', 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.accounts.edit', ['account' => $id], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('account.delete');
        $account = $this->accountsService->find($id);
        $this->accountsService->delete($account);
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.accounts.index', [], ['messege' => 'Account deleted successfully', 'alert-type' => 'success']);
    }

    public function cashflow()
    {
        checkAdminHasPermissionAndThrowException('cash.flow.view');

        // Check if date filter is applied - show all-time data by default
        $hasDateFilter = request('from_date') || request('to_date');
        $fromDate = request('from_date') ? now()->parse(request('from_date'))->startOfDay() : null;
        $toDate = request('to_date') ? now()->parse(request('to_date'))->endOfDay() : null;

        $data = [];

        // Helper function to apply date filter
        $applyDateFilter = function ($query, $dateColumn) use ($hasDateFilter, $fromDate, $toDate) {
            if ($hasDateFilter) {
                if ($fromDate && $toDate) {
                    $query->whereBetween($dateColumn, [$fromDate, $toDate]);
                } elseif ($fromDate) {
                    $query->where($dateColumn, '>=', $fromDate);
                } elseif ($toDate) {
                    $query->where($dateColumn, '<=', $toDate);
                }
            }
            return $query;
        };

        // Service Sale
        $serviceSaleQuery = ProductSale::whereNotNull('service_id');
        if ($hasDateFilter) {
            $serviceSaleQuery->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                if ($fromDate && $toDate) {
                    $q->whereBetween('order_date', [$fromDate, $toDate]);
                } elseif ($fromDate) {
                    $q->where('order_date', '>=', $fromDate);
                } elseif ($toDate) {
                    $q->where('order_date', '<=', $toDate);
                }
            });
        }
        $data['serviceSale'] = $serviceSaleQuery->sum('sub_total');

        // Product Sale
        $productSaleQuery = CustomerPayment::where('payment_type', 'sale');
        $applyDateFilter($productSaleQuery, 'payment_date');
        $data['productSale'] = $productSaleQuery->sum('amount') - $data['serviceSale'];

        // Customer Due
        $customerDueQuery = CustomerPayment::whereIn('payment_type', ['due_receive', 'direct_due_receive']);
        $applyDateFilter($customerDueQuery, 'payment_date');
        $data['customer_due'] = $customerDueQuery->sum('amount');

        // Sale Return (actual cash refunded to customer)
        $saleReturnQuery = CustomerPayment::where('payment_type', 'sale return');
        $applyDateFilter($saleReturnQuery, 'payment_date');
        $data['sale_return'] = $saleReturnQuery->sum('amount');

        // Balance Deposit
        $balanceDepositQuery = Balance::where('balance_type', 'deposit');
        $applyDateFilter($balanceDepositQuery, 'date');
        $data['balance_deposit'] = $balanceDepositQuery->sum('amount');

        // Balance Withdraw
        $balanceWithdrawQuery = Balance::where('balance_type', 'withdraw');
        $applyDateFilter($balanceWithdrawQuery, 'date');
        $data['balance_withdraw'] = $balanceWithdrawQuery->sum('amount');

        // Customer Advance
        $customerAdvanceQuery = CustomerPayment::where('payment_type', 'advance_receive');
        $applyDateFilter($customerAdvanceQuery, 'payment_date');
        $data['customer_advance'] = $customerAdvanceQuery->sum('amount');

        // Customer Advance Refund
        $customerAdvanceRefundQuery = CustomerPayment::where('payment_type', 'advance_refund');
        $applyDateFilter($customerAdvanceRefundQuery, 'payment_date');
        $data['customer_advance_refund'] = $customerAdvanceRefundQuery->sum('amount');

        // Salary
        $salaryQuery = EmployeeSalary::query();
        $applyDateFilter($salaryQuery, 'date');
        $data['salary'] = $salaryQuery->sum('amount');

        // Expenses (legacy expenses WITHOUT supplier AND without payment records)
        $legacyExpensesQuery = Expense::whereNull('expense_supplier_id')
            ->whereDoesntHave('payments');
        $applyDateFilter($legacyExpensesQuery, 'date');
        $legacyExpenses = $legacyExpensesQuery->sum('amount');

        // Direct expenses (non-supplier expenses with payment records)
        $directExpenseQuery = ExpenseSupplierPayment::where('payment_type', 'direct_expense');
        $applyDateFilter($directExpenseQuery, 'payment_date');
        $directExpenses = $directExpenseQuery->sum('amount');

        $data['expenses'] = $legacyExpenses + $directExpenses;

        // Supplier Due Pay
        $supplierDuePayQuery = SupplierPayment::where('payment_type', 'due_pay');
        $applyDateFilter($supplierDuePayQuery, 'payment_date');
        $data['supplierDuePay'] = $supplierDuePayQuery->sum('amount');

        // Supplier Advance Pay
        $supplierAdvancePayQuery = SupplierPayment::where('payment_type', 'advance_pay');
        $applyDateFilter($supplierAdvancePayQuery, 'payment_date');
        $data['supplierAdvancePay'] = $supplierAdvancePayQuery->sum('amount');

        // Supplier Advance Refund
        $supplierAdvanceRefundQuery = SupplierPayment::where('payment_type', 'advance_refund');
        $applyDateFilter($supplierAdvanceRefundQuery, 'payment_date');
        $data['supplierAdvanceRefund'] = $supplierAdvanceRefundQuery->sum('amount');

        // Purchase
        $purchaseQuery = SupplierPayment::where('payment_type', 'purchase');
        $applyDateFilter($purchaseQuery, 'payment_date');
        $data['purchase'] = $purchaseQuery->sum('amount');

        // Purchase Return (money received from supplier)
        $purchaseReturnQuery = SupplierPayment::where('payment_type', 'purchase_receive');
        $applyDateFilter($purchaseReturnQuery, 'payment_date');
        $data['purchaseReturn'] = $purchaseReturnQuery->sum('amount');

        // Expense Supplier Due Pay
        $expenseSupplierDuePayQuery = ExpenseSupplierPayment::where('payment_type', 'due_pay');
        $applyDateFilter($expenseSupplierDuePayQuery, 'payment_date');
        $data['expenseSupplierDuePay'] = $expenseSupplierDuePayQuery->sum('amount');

        // Expense Supplier Advance Pay
        $expenseSupplierAdvancePayQuery = ExpenseSupplierPayment::where('payment_type', 'advance_pay');
        $applyDateFilter($expenseSupplierAdvancePayQuery, 'payment_date');
        $data['expenseSupplierAdvancePay'] = $expenseSupplierAdvancePayQuery->sum('amount');

        // Expense Supplier Advance Refund
        $expenseSupplierAdvanceRefundQuery = ExpenseSupplierPayment::where('payment_type', 'advance_refund');
        $applyDateFilter($expenseSupplierAdvanceRefundQuery, 'payment_date');
        $data['expenseSupplierAdvanceRefund'] = $expenseSupplierAdvanceRefundQuery->sum('amount');

        // Expense Payment (paid amount at time of expense creation)
        $expensePaymentQuery = ExpenseSupplierPayment::where('payment_type', 'expense');
        $applyDateFilter($expensePaymentQuery, 'payment_date');
        $data['expenseSupplierPayment'] = $expensePaymentQuery->sum('amount');

        // Balance Transfers (for visibility - these are internal movements)
        $balanceTransferQuery = BalanceTransfer::query();
        $applyDateFilter($balanceTransferQuery, 'date');
        $data['balance_transfer'] = $balanceTransferQuery->sum('amount');

        $data['totalPay'] = $data['sale_return'] + $data['balance_withdraw'] + $data['customer_advance_refund'] + $data['supplierDuePay'] + $data['supplierAdvancePay'] + $data['purchase'] + $data['expenses'] + $data['salary'] + $data['expenseSupplierDuePay'] + $data['expenseSupplierAdvancePay'] + $data['expenseSupplierPayment'];

        $data['totalReceive'] = $data['productSale']  + $data['balance_deposit'] + $data['customer_advance'] + $data['customer_due'] + $data['supplierAdvanceRefund'] + $data['serviceSale'] + $data['purchaseReturn'] + $data['expenseSupplierAdvanceRefund'];

        // Opening balance is 0 for all-time view, or calculated from the start date when filtered
        $openingBalance = $hasDateFilter && $fromDate ? $this->accountsService->getOpeningBalance($fromDate) : 0;

        $currentBalance = $openingBalance + $data['totalReceive'] - $data['totalPay'];
        return view('accounts::cash-flow', compact('data', 'openingBalance', 'currentBalance', 'hasDateFilter'));
    }

    public function ledger($id)
    {
        checkAdminHasPermissionAndThrowException('account.view');

        $account = $this->accountsService->find($id);

        // Get account display name
        $accountName = match($account->account_type) {
            'cash' => __('Cash'),
            'bank' => $account->bank?->name . ' - ' . $account->bank_account_number,
            'mobile_banking' => $account->mobile_bank_name . ' - ' . $account->mobile_number,
            'card' => $account->card_type . ' - ' . $account->card_number,
            default => $account->account_type
        };

        $title = __('Account Ledger') . ' - ' . $accountName;

        // Date filter
        $hasDateFilter = request('from_date') || request('to_date');
        $fromDate = request('from_date') ? now()->parse(request('from_date')) : null;
        $toDate = request('to_date') ? now()->parse(request('to_date')) : null;

        // Collect all transactions
        $transactions = collect();

        // Helper to apply date filter
        $applyDateFilter = function ($query, $dateColumn) use ($hasDateFilter, $fromDate, $toDate) {
            if ($hasDateFilter) {
                if ($fromDate && $toDate) {
                    $query->whereBetween($dateColumn, [$fromDate, $toDate]);
                } elseif ($fromDate) {
                    $query->where($dateColumn, '>=', $fromDate);
                } elseif ($toDate) {
                    $query->where($dateColumn, '<=', $toDate);
                }
            }
            return $query;
        };

        // Customer Payments (Sales, Due Receive, Advance, etc.)
        $customerPaymentsQuery = $account->customerPayments();
        $applyDateFilter($customerPaymentsQuery, 'payment_date');
        $customerPayments = $customerPaymentsQuery->get()->map(function ($payment) {
            $url = null;
            if ($payment->sale_id) {
                $url = route('admin.sales.invoice', $payment->sale_id);
            }
            return [
                'date' => $payment->payment_date,
                'description' => ucfirst(str_replace('_', ' ', $payment->payment_type)),
                'reference' => $payment->sale?->invoice ?? $payment->customer?->name ?? '-',
                'url' => $url,
                'debit' => $payment->is_received ? $payment->amount : 0,
                'credit' => $payment->is_paid ? $payment->amount : 0,
            ];
        });
        $transactions = $transactions->merge($customerPayments);

        // Supplier Payments (Purchase, Due Pay, Advance, etc.)
        $supplierPaymentsQuery = $account->supplierPayments();
        $applyDateFilter($supplierPaymentsQuery, 'payment_date');
        $supplierPayments = $supplierPaymentsQuery->get()->map(function ($payment) {
            $url = null;
            if ($payment->purchase_id) {
                $url = route('admin.purchase.invoice', $payment->purchase_id);
            }
            return [
                'date' => $payment->payment_date,
                'description' => ucfirst(str_replace('_', ' ', $payment->payment_type)),
                'reference' => $payment->purchase?->invoice ?? $payment->supplier?->name ?? '-',
                'url' => $url,
                'debit' => $payment->is_received ? $payment->amount : 0,
                'credit' => $payment->is_paid ? $payment->amount : 0,
            ];
        });
        $transactions = $transactions->merge($supplierPayments);

        // Expense Supplier Payments
        $expensePaymentsQuery = $account->expenseSupplierPayments();
        $applyDateFilter($expensePaymentsQuery, 'payment_date');
        $expensePayments = $expensePaymentsQuery->get()->map(function ($payment) {
            return [
                'date' => $payment->payment_date,
                'description' => __('Expense') . ' - ' . ucfirst(str_replace('_', ' ', $payment->payment_type)),
                'reference' => $payment->expense?->invoice ?? '-',
                'url' => null,
                'debit' => $payment->is_received ? $payment->amount : 0,
                'credit' => $payment->is_paid ? $payment->amount : 0,
            ];
        });
        $transactions = $transactions->merge($expensePayments);

        // Balance Deposits
        $depositsQuery = $account->deposits();
        $applyDateFilter($depositsQuery, 'date');
        $deposits = $depositsQuery->get()->map(function ($balance) {
            return [
                'date' => $balance->date,
                'description' => __('Balance Deposit'),
                'reference' => $balance->note ?? '-',
                'url' => null,
                'debit' => $balance->amount,
                'credit' => 0,
            ];
        });
        $transactions = $transactions->merge($deposits);

        // Balance Withdraws
        $withdrawsQuery = $account->withdraws();
        $applyDateFilter($withdrawsQuery, 'date');
        $withdraws = $withdrawsQuery->get()->map(function ($balance) {
            return [
                'date' => $balance->date,
                'description' => __('Balance Withdraw'),
                'reference' => $balance->note ?? '-',
                'url' => null,
                'debit' => 0,
                'credit' => $balance->amount,
            ];
        });
        $transactions = $transactions->merge($withdraws);

        // Balance Transfers In
        $transfersInQuery = $account->transfersIn();
        $applyDateFilter($transfersInQuery, 'date');
        $transfersIn = $transfersInQuery->get()->map(function ($transfer) {
            return [
                'date' => $transfer->date,
                'description' => __('Transfer In'),
                'reference' => $transfer->note ?? '-',
                'url' => route('admin.balance.transfer'),
                'debit' => $transfer->amount,
                'credit' => 0,
            ];
        });
        $transactions = $transactions->merge($transfersIn);

        // Balance Transfers Out
        $transfersOutQuery = $account->transfersOut();
        $applyDateFilter($transfersOutQuery, 'date');
        $transfersOut = $transfersOutQuery->get()->map(function ($transfer) {
            return [
                'date' => $transfer->date,
                'description' => __('Transfer Out'),
                'reference' => $transfer->note ?? '-',
                'url' => route('admin.balance.transfer'),
                'debit' => 0,
                'credit' => $transfer->amount,
            ];
        });
        $transactions = $transactions->merge($transfersOut);

        // Salary Payments
        $salaryQuery = $account->salary();
        $applyDateFilter($salaryQuery, 'date');
        $salaries = $salaryQuery->get()->map(function ($salary) {
            return [
                'date' => $salary->date,
                'description' => __('Salary Payment'),
                'reference' => $salary->employee?->name ?? '-',
                'url' => null,
                'debit' => 0,
                'credit' => $salary->amount,
            ];
        });
        $transactions = $transactions->merge($salaries);

        // Legacy Expenses (without supplier)
        $expensesQuery = $account->expenses();
        $applyDateFilter($expensesQuery, 'date');
        $expenses = $expensesQuery->get()->map(function ($expense) {
            return [
                'date' => $expense->date,
                'description' => __('Expense') . ' - ' . ($expense->expenseType?->name ?? ''),
                'reference' => $expense->invoice ?? '-',
                'url' => null,
                'debit' => 0,
                'credit' => $expense->amount,
            ];
        });
        $transactions = $transactions->merge($expenses);

        // Sort by date
        $transactions = $transactions->sortBy('date')->values();

        // Calculate opening balance if date filter applied
        $openingBalance = $hasDateFilter && $fromDate ? $account->getOpeningBalance($fromDate) : 0;

        // Apply keyword filter
        if (request('keyword')) {
            $keyword = strtolower(request('keyword'));
            $transactions = $transactions->filter(function ($item) use ($keyword) {
                return str_contains(strtolower($item['description']), $keyword) ||
                       str_contains(strtolower($item['reference']), $keyword);
            })->values();
        }

        // Calculate totals
        $totalDebit = $transactions->sum('debit');
        $totalCredit = $transactions->sum('credit');
        $closingBalance = $openingBalance + $totalDebit - $totalCredit;

        // Excel Export
        if (request('export') == 'excel') {
            $fileName = 'account-ledger-' . $account->id . '-' . date('Y-m-d') . '.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\AccountLedgerExport($transactions, $title, $openingBalance, $totalDebit, $totalCredit, $closingBalance),
                $fileName
            );
        }

        // PDF Export
        if (request('export') == 'pdf') {
            return view('accounts::pdf.ledger', compact('transactions', 'title', 'account', 'openingBalance', 'totalDebit', 'totalCredit', 'closingBalance', 'hasDateFilter'));
        }

        // Pagination
        $perPage = request('par-page', 20);
        if ($perPage === 'all') {
            $ledgers = $transactions;
        } else {
            $page = request('page', 1);
            $ledgers = new \Illuminate\Pagination\LengthAwarePaginator(
                $transactions->forPage($page, $perPage),
                $transactions->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return view('accounts::ledger', compact('ledgers', 'title', 'account', 'openingBalance', 'totalDebit', 'totalCredit', 'closingBalance', 'hasDateFilter'));
    }
}
