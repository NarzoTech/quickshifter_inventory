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
    public function __construct(private AccountsService $account) {}
    /**
     * Display a listing of the resource.
     */
    public function openingBalance()
    {
        $accounts = $this->account->all()->get();
        $deposits = Balance::where('balance_type', 'deposit')->get();
        $withdraws = Balance::where('balance_type', 'withdraw')->get();
        return view('accounts::balance', compact('accounts', 'deposits', 'withdraws'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('accounts::create');
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
        return view('accounts::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
