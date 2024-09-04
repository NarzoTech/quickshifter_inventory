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
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\SalesReturn;

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
            $accountBalance += $account->balance();
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
        //
    }

    public function cashflow()
    {
        $data = [];
        $data['productSale'] = ProductSale::whereNotNull('product_id')->where('source', 1)->sum('sub_total');
        $data['serviceSale'] = ProductSale::whereNotNull('service_id')->sum('sub_total');
        $data['customer_due'] = Sale::whereNotNull('customer_id')->sum('due_amount');
        $data['sale_return'] = SalesReturn::sum('return_amount');
        $data['balance_deposit'] = Balance::where('balance_type', 'deposit')->sum('amount');
        $data['balance_withdraw'] = Balance::where('balance_type', 'withdraw')->sum('amount');
        $data['customer_advance'] = CustomerPayment::where('payment_type', 'advance_receive')->sum('amount');
        $data['customer_advance_refund'] = CustomerPayment::where('payment_type', 'advance_refund')->sum('amount');


        return view('accounts::cash-flow', compact('data'));
    }
}
