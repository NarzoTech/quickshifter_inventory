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
        $types = ExpenseType::paginate(20);
        return view('expense::type',compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expense::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $type = new ExpenseType();
            $type->name = $request->name;
            $type->save();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.expense.type.index', [], ['messege' => 'Expense Type Created Successfully', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.expense.type.create', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('expense::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('expense::edit');
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
