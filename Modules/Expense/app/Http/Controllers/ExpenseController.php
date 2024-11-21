<?php

namespace Modules\Expense\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Exports\ExpensesExport;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\app\Models\Account;
use Modules\Expense\app\Http\Requests\ExpenseRequest;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseType;
use Modules\Expense\app\Services\ExpenseService;

class ExpenseController extends Controller
{

    use RedirectHelperTrait;
    public function __construct(private ExpenseService $expense)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::query();

        if (request('keyword')) {
            $keyword = request('keyword');
            $expenses = $expenses->where(function ($query) use ($keyword) {
                $query->where('amount', 'like', "%{$keyword}%")
                    ->orWhereHas('expenseType', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhere('amount', 'like', "%{$keyword}%")
                    ->orWhereHas('account', function ($q) use ($keyword) {
                        $q->where('account_type', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('createdBy', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
            });
        }
        if (request('order_type')) {
            $expenses = $expenses->orderBy(request('order_type'), request('order_by'));
        } else {
            $expenses = $expenses->orderBy('id', 'desc');
        }
        if (request('from_date') && request('to_date')) {
            $from = now()->parse(request('from_date'));
            $to = now()->parse(request('to_date'));
            $expenses = $expenses->whereBetween('date', [$from, $to]);
        }

        if (request('export')) {
            $fileName = 'expenses-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
            return Excel::download(new ExpensesExport($expenses->get()), $fileName);
        }
        $totalAmount = $expenses->sum('amount');

        if (request('par_page')) {
            $expenses = $expenses->paginate(request('par_page'));
        } else {
            $expenses = $expenses->paginate(20);
        }

        $types = ExpenseType::all();
        $accounts = Account::all();
        return view('expense::index', compact('expenses', 'types', 'accounts', 'totalAmount'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExpenseRequest $request): RedirectResponse
    {
        try {
            $this->expense->store($request);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.expense.index', [], ['messege' => 'Expense created successfully', 'alert-type' => 'success']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, null, [], ['messege' => $exception->getMessage(), 'alert-type' => 'danger']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $this->expense->update($request, $id);
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.expense.index', [], ['messege' => 'Expense updated successfully', 'alert-type' => 'success']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, null, [], ['messege' => $exception->getMessage(), 'alert-type' => 'danger']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->expense->destroy($id);
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.expense.index', [], ['messege' => 'Expense deleted successfully', 'alert-type' => 'success']);
    }
}
