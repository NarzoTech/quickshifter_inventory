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
use Modules\Accounts\app\Services\AccountsService;
use Modules\Accounts\app\Services\BankService;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Employee\app\Models\EmployeeSalary;
use Modules\Expense\app\Models\Expense;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\SalesReturn;
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
        $accounts = $this->accountsService->all()->paginate(20);
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
        $accounts = $this->bankService->all()->get();
        return view('accounts::create', compact('accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AccountRequest $request): RedirectResponse
    {
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
        $account = $this->accountsService->find($id);
        $accounts = $this->bankService->all()->get();
        return view('accounts::edit', compact('account', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AccountRequest $request, $id): RedirectResponse
    {
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
        $account = $this->accountsService->find($id);
        $this->accountsService->delete($account);
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.accounts.index', [], ['messege' => 'Account deleted successfully', 'alert-type' => 'success']);
    }

    public function cashflow()
    {
        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        $data = [];

        $data['productSale'] = CustomerPayment::where('payment_type', 'sale')->whereBetween('payment_date', [$fromDate, $toDate])->sum('amount');

        // $data['serviceSale'] = ProductSale::whereHas('sale', function ($q) use ($fromDate, $toDate) {
        //     $q->whereBetween('order_date', [$fromDate, $toDate]);
        // })->whereNotNull('service_id')->sum('sub_total');

        $data['customer_due'] = Sale::whereBetween('order_date', [$fromDate, $toDate])->whereNotNull('customer_id')->sum('due_amount');

        $data['sale_return'] = SalesReturn::whereBetween('return_date', [$fromDate, $toDate])->sum('return_amount');
        $data['balance_deposit'] = Balance::where('balance_type', 'deposit')->whereBetween('date', [$fromDate, $toDate])->sum('amount');

        $data['balance_withdraw'] = Balance::where('balance_type', 'withdraw')->whereBetween('date', [$fromDate, $toDate])->sum('amount');

        $data['customer_advance'] = CustomerPayment::where('payment_type', 'advance_receive')->whereBetween('payment_date', [$fromDate, $toDate])->sum('amount');

        $data['customer_advance_refund'] = CustomerPayment::where('payment_type', 'advance_refund')->whereBetween('payment_date', [$fromDate, $toDate])->sum('amount');

        $data['salary'] = EmployeeSalary::whereBetween('date', [$fromDate, $toDate])->sum('amount');
        $data['expenses'] = Expense::whereBetween('date', [$fromDate, $toDate])->sum('amount');

        $data['supplierDuePay'] = SupplierPayment::where('payment_type', 'due_pay')->whereBetween('payment_date', [$fromDate, $toDate])->sum('amount');

        $data['supplierAdvancePay'] = SupplierPayment::where('payment_type', 'advance_pay')->whereBetween('payment_date', [$fromDate, $toDate])->sum('amount');

        $data['supplierAdvanceRefund'] = SupplierPayment::where('payment_type', 'advance_refund')->whereBetween('payment_date', [$fromDate, $toDate])->sum('amount');

        $data['purchase'] = SupplierPayment::where('payment_type', 'purchase')->whereBetween('payment_date', [$fromDate, $toDate])->sum('amount');

        $data['totalPay'] = $data['sale_return'] + $data['balance_withdraw'] + $data['customer_advance_refund'] + $data['supplierDuePay'] + $data['supplierAdvancePay'] + $data['purchase'] + $data['expenses'] + $data['salary'];

        $data['totalReceive'] = $data['productSale']  + $data['balance_deposit'] + $data['customer_advance'] + $data['customer_due'] + $data['supplierAdvanceRefund'];

        $openingBalance = $this->accountsService->getOpeningBalance($fromDate);
        // $currentBalance = $this->accountsService->accountBalance($fromDate, $toDate) + $openingBalance;

        $currentBalance = $openingBalance + $data['totalReceive'] - $data['totalPay'];
        return view('accounts::cash-flow', compact('data', 'openingBalance', 'currentBalance'));
    }
}
