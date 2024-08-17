<?php

namespace Modules\Accounts\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Accounts\app\Services\AccountsService;

class BalanceController extends Controller
{
    public function __construct(private AccountsService $account)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function openingBalance()
    {
        $accounts = $this->account->all()->get();
        $deposits = Balance::where('balance_type', 'deposit')->paginate(20);
        $withdraws = Balance::where('balance_type', 'withdraw')->paginate(20);

        $totalDeposits = Balance::where('balance_type', 'deposit')->sum('amount');
        $totalWithdraws = Balance::where('balance_type', 'withdraw')->sum('amount');

        $accountBalance = 0;
        $accounts->map(function ($account) use (&$accountBalance) {
            $accountBalance += $account->balance();
        });
        return view('accounts::balance', compact('accounts', 'deposits', 'withdraws', 'totalDeposits', 'totalWithdraws', 'accountBalance'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            if ($request->payment_type == 'cash' || $request->payment_type == 'advance') {
                $account = $this->account->all()->where('account_type', 'cash')->first();
            } else {
                $account = $this->account->find($request->account_id);
            }

            // balance

            $balance = Balance::create([
                'balance_type' => $request->balance_type,
                'date' => now()->parse($request->date),
                'amount' => $request->amount,
                'account_id' => $account->id,
                'note' => $request->note,
                'payment_type' => $request->payment_type,
                'type_id' => $request->type_id,
                'created_by' => auth('admin')->id(),
            ]);

            return back()->with(['messege' => ucfirst($request->balance_type) . " created successfully.", 'alert-type' => 'success']);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());

            return back()->with(['messege' => 'Something went wrong.', 'alert-type' => 'danger']);
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
        $accounts = $this->account->all()->orderBy('id', 'desc')->get();
        $deposits = Balance::where('balance_type', 'deposit')->paginate(20);
        $withdraws = Balance::where('balance_type', 'withdraw')->paginate(20);
        $totalDeposits = Balance::where('balance_type', 'deposit')->sum('amount');
        $totalWithdraws = Balance::where('balance_type', 'withdraw')->sum('amount');
        $balance = Balance::find($id);

        $accountBalance = 0;
        $accounts->map(function ($account) use (&$accountBalance) {

            $accountBalance += $account->balance();
        });
        return view('accounts::balance-edit', compact('accounts', 'deposits', 'withdraws', 'balance', 'totalDeposits', 'totalWithdraws', 'accountBalance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        if ($request->payment_type == 'cash' || $request->payment_type == 'advance') {
            $account = $this->account->all()->where('account_type', 'cash')->first();
        } else {
            $account = $this->account->find($request->account_id);
        }

        $balance = Balance::find($id);

        $data = $request->except('_token');
        $data['updated_by'] = auth('admin')->id();
        $data['account_id'] = $account->id;
        $data['date'] = now()->parse($request->date);
        $balance->update($data);
        return to_route('admin.opening-balance')->with(['messege' => 'Balance updated successfully.', 'alert-type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $balance = Balance::find($id);
        $balance->delete();
        return back()->with(['messege' => 'Balance deleted successfully.', 'alert-type' => 'success']);
    }
}
