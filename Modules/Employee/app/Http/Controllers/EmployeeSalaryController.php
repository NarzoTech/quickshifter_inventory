<?php

namespace Modules\Employee\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Employee\app\Http\Requests\EmployeeSalaryRequest;
use Modules\Employee\app\Services\EmployeeService;
use Modules\Purchase\app\Services\PurchaseService;

class EmployeeSalaryController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private PurchaseService $purchaseService, private EmployeeService $employee)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('employee::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $accounts = $this->purchaseService->getAccounts();
        $employee = $this->employee->find($id);
        return view('employee::salary.create', compact('accounts', 'employee'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeSalaryRequest $request, $id): RedirectResponse
    {
        try {
            $employee = $this->employee->find($id);
            $this->employee->addSalary($request, $employee);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.employee.index', [], ['messege' => 'Employee salary added successfully', 'alert-type' => 'success']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.employee.index', [], ['messege' => $ex->getMessage(), 'alert-type' => 'error']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('employee::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('employee::edit');
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

    public function salaryInfo(Request $request, $id)
    {
        $employee = $this->employee->find($id);
        return ['advanceAmount' => $employee->getAdvanceAmountAttribute($request->month, $request->year), 'dueAmount' => $employee->getDueAmountAttribute($request->month, $request->year)];
    }
}
