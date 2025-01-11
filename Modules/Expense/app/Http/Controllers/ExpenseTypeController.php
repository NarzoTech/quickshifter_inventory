<?php

namespace Modules\Expense\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Expense\app\Models\ExpenseType;

class ExpenseTypeController extends Controller
{
    use RedirectHelperTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('expense.type.view');
        $types = ExpenseType::query();

        if (request('keyword')) {
            $keyword = request('keyword');
            $types = $types->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            });
        }
        if (request('order_type')) {
            $types = $types->orderBy(request('order_type'), request('order_by'));
        } else {
            $types = $types->orderBy('id', 'desc');
        }
        if (request('par_page')) {
            $types = $types->paginate(request('par_page'));
        } else {
            $types = $types->paginate(20);
        }

        $types->appends(request()->query());

        return view('expense::type', compact('types'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('expense.type.create');
        try {
            $type = new ExpenseType();
            $type->name = $request->name;
            $type->save();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.expense.type.index', [], ['messege' => 'Expense Type Created Successfully', 'alert-type' => 'success']);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.expense.type.create', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('expense.type.edit');
        try {
            $type = ExpenseType::find($id);
            $type->name = $request->name;
            $type->save();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.expense.type.index', [], ['messege' => 'Expense Type Updated Successfully', 'alert-type' => 'success']);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.expense.type.index', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('expense.type.delete');
        try {
            $type = ExpenseType::find($id);
            $type->delete();
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.expense.type.index', [], ['messege' => 'Expense Type Deleted Successfully', 'alert-type' => 'success']);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.expense.type.index', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }
}
