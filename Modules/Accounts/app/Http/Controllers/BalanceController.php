<?php

namespace Modules\Accounts\app\Http\Controllers;

use App\Exports\BalanceTransferExport;
use App\Http\Controllers\Controller;
use App\Models\Balance;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Modules\Accounts\app\Models\Account;
use Modules\Accounts\app\Models\BalanceTransfer;
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
        checkAdminHasPermissionAndThrowException('deposit.withdraw.view');
        $accounts = $this->account->all()->get();
        $deposits = Balance::with('account')->where('balance_type', 'deposit')->paginate(20);
        $deposits->appends(request()->query());

        $withdraws = Balance::with('account')->where('balance_type', 'withdraw')->paginate(20);
        $withdraws->appends(request()->query());

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
        checkAdminHasPermissionAndThrowException('deposit.withdraw.create');

        $request->validate([
            'balance_type' => 'required|in:deposit,withdraw',
            'date'         => 'required|date',
            'amount'       => 'required|numeric|min:0.01',
            'payment_type' => 'required|string',
        ]);

        try {
            if ($request->payment_type == 'cash' || $request->payment_type == 'advance') {
                $account = $this->account->all()->where('account_type', 'cash')->first();
            } else {
                if (!$request->account_id) {
                    return back()->with(['message' => 'Please select an account', 'alert-type' => 'error']);
                }
                $account = $this->account->find((int) $request->account_id);
            }

            $balance = Balance::create([
                'balance_type' => $request->balance_type,
                'date' => now()->parse($request->date),
                'amount' => round((float) $request->amount, 2),
                'account_id' => $account->id,
                'note' => $request->note,
                'payment_type' => $request->payment_type,
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
        checkAdminHasPermissionAndThrowException('deposit.withdraw.edit');
        $accounts = $this->account->all()->orderBy('id', 'desc')->get();
        $deposits = Balance::with('account')->where('balance_type', 'deposit')->paginate(20);
        $deposits->appends(request()->query());

        $withdraws = Balance::with('account')->where('balance_type', 'withdraw')->paginate(20);
        $withdraws->appends(request()->query());

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
        checkAdminHasPermissionAndThrowException('deposit.withdraw.edit');

        $request->validate([
            'balance_type' => 'required|in:deposit,withdraw',
            'date'         => 'required|date',
            'amount'       => 'required|numeric|min:0.01',
            'payment_type' => 'required|string',
        ]);

        if ($request->payment_type == 'cash' || $request->payment_type == 'advance') {
            $account = $this->account->all()->where('account_type', 'cash')->first();
        } else {
            $account = $this->account->find($request->account_id);
        }

        $balance = Balance::find($id);

        // Only update safe fields — prevent mass assignment of arbitrary fields
        $balance->update([
            'balance_type' => $request->balance_type,
            'amount'       => round((float) $request->amount, 2),
            'payment_type' => $request->payment_type,
            'note'         => $request->note,
            'updated_by'   => auth('admin')->id(),
            'account_id'   => $account->id,
            'date'         => now()->parse($request->date),
        ]);
        return to_route('admin.opening-balance')->with(['messege' => 'Balance updated successfully.', 'alert-type' => 'success']);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('deposit.withdraw.delete');
        $balance = Balance::find($id);
        $balance->delete();
        return back()->with(['messege' => 'Balance deleted successfully.', 'alert-type' => 'success']);
    }

    public function transfer()
    {
        checkAdminHasPermissionAndThrowException('balance.transfer.view');

        $accounts = $this->account->all()->get();

        $sort = request('order_by') == 'asc' ? 'asc' : 'desc';
        $transfers = BalanceTransfer::query()->orderBy('date', $sort);

        if (request('par-page')) {
            if (request('par-page') == 'all') {
                $transfers = $transfers->get();
            } else {
                $transfers = $transfers->paginate(request('par-page'));
                $transfers->appends(request()->query());
            }
        } else {
            $transfers = $transfers->paginate(20);
            $transfers->appends(request()->query());
        }

        if (checkAdminHasPermission('balance.transfer.excel.download')) {
            if (request('export')) {
                $fileName = 'transfer-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new BalanceTransferExport($transfers), $fileName);
            }
        }

        if (checkAdminHasPermission('balance.transfer.pdf.download')) {
            if (request('export_pdf')) {
                return view('accounts::pdf.transfer', [
                    'transfers' => $transfers,
                ]);
            }
        }

        return view('accounts::balance-transfer', compact('accounts', 'transfers'));
    }


    public function transferStore(Request $request)
    {
        checkAdminHasPermissionAndThrowException('balance.transfer.create');

        // Validate required fields
        $request->validate([
            'date' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'from_account_type' => 'required',
            'to_account_type' => 'required',
            'from_account' => 'required_unless:from_account_type,cash',
            'to_account' => 'required_unless:to_account_type,cash',
        ]);

        $data = $request->except('_token');
        $data['created_by'] = auth('admin')->id();
        $data['date'] = now()->parse($request->date);


        // from account

        $fromAccount = Account::where('account_type', $request->from_account_type);
        if ($request->from_account_type == 'cash') {
            $fromAccount = $fromAccount->first();
        } else {
            $fromAccount = $fromAccount->where('id', $request->from_account)->first();
        }

        if (!$fromAccount) {
            return back()->with(['messege' => 'From account not found.', 'alert-type' => 'error']);
        }

        $data['from_account_id'] = $fromAccount->id;

        // to account

        $toAccount = Account::where('account_type', $request->to_account_type);
        if ($request->to_account_type == 'cash') {
            $toAccount = $toAccount->first();
        } else {
            $toAccount = $toAccount->where('id', $request->to_account)->first();
        }

        if (!$toAccount) {
            return back()->with(['messege' => 'To account not found. Please make sure the account exists.', 'alert-type' => 'error']);
        }

        $data['to_account_id'] = $toAccount->id;

        // Prevent transfer to same account
        if ($fromAccount->id === $toAccount->id) {
            return back()->with(['messege' => 'Cannot transfer to the same account.', 'alert-type' => 'error']);
        }

        DB::beginTransaction();
        try {
            BalanceTransfer::create($data);
            DB::commit();
            return back()->with(['messege' => 'Balance transfer created successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with(['messege' => 'Balance transfer failed.', 'alert-type' => 'error']);
        }
    }

    public function transferUpdate(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('balance.transfer.edit');

        // Validate required fields
        $request->validate([
            'date' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'from_account_type' => 'required',
            'to_account_type' => 'required',
            'from_account' => 'required_unless:from_account_type,cash',
            'to_account' => 'required_unless:to_account_type,cash',
        ]);

        $data = $request->except('_token');

        $data['date'] = now()->parse($request->date);
        $balance = BalanceTransfer::find($id);


        // from account

        $fromAccount = Account::where('account_type', $request->from_account_type);
        if ($request->from_account_type == 'cash') {
            $fromAccount = $fromAccount->first();
        } else {
            $fromAccount = $fromAccount->where('id', $request->from_account)->first();
        }

        if (!$fromAccount) {
            return back()->with(['messege' => 'From account not found.', 'alert-type' => 'error']);
        }

        $data['from_account_id'] = $fromAccount->id;


        $toAccount = Account::where('account_type', $request->to_account_type);
        if ($request->to_account_type == 'cash') {
            $toAccount = $toAccount->first();
        } else {
            $toAccount = $toAccount->where('id', $request->to_account)->first();
        }

        if (!$toAccount) {
            return back()->with(['messege' => 'To account not found. Please make sure the account exists.', 'alert-type' => 'error']);
        }

        $data['to_account_id'] = $toAccount->id;

        // Prevent transfer to same account
        if ($fromAccount->id === $toAccount->id) {
            return back()->with(['messege' => 'Cannot transfer to the same account.', 'alert-type' => 'error']);
        }

        DB::beginTransaction();
        try {
            $balance->update($data);
            DB::commit();
            return back()->with(['messege' => 'Balance transfer updated successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with(['messege' => 'Balance transfer update failed.', 'alert-type' => 'error']);
        }
    }


    public function transferDestroy($id)
    {
        checkAdminHasPermissionAndThrowException('balance.transfer.delete');
        $balance = BalanceTransfer::find($id);
        $balance->delete();
        return back()->with(['messege' => 'Balance transfer deleted successfully.', 'alert-type' => 'success']);
    }
}
