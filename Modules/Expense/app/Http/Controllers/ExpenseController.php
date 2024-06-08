<?php

namespace Modules\Expense\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Accounts\app\Models\Account;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseType;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::paginate(20);
        $types = ExpenseType::all();
        $accounts = Account::all();
        return view('expense::index', compact('expenses', 'types', 'accounts'));
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
        //
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
