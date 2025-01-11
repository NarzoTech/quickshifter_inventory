<?php

namespace Modules\Employee\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Attendance\app\Models\HolidaySetup;
use Modules\Attendance\app\Models\WeekendSetup;
use Modules\Employee\app\Http\Requests\EmployeeSalaryRequest;
use Modules\Employee\app\Models\EmployeeSalary;
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
    public function index(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('employee.view.payment');
        [$payments, $employee, $month, $payableSalary, $totalAttendance, $totalDayOff] = $this->employee->calculateSalary($request, $id);

        return view('employee::salary.index', compact('payments', 'employee', 'month', 'payableSalary', 'totalAttendance', 'totalDayOff'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        if (!checkAdminHasPermission('employee.pay.salary') || !checkAdminHasPermission('employee.pay.advance')) {
            abort(403);
        }

        $accounts = $this->purchaseService->getAccounts();
        $employee = $this->employee->find($id);
        [$payments, $employee, $month, $payableSalary, $totalAttendance, $totalDayOff] = $this->employee->calculateSalary(request(), $id);
        $paidAmount = $employee->getPaidAmountAttribute();
        return view('employee::salary.create', compact('accounts', 'employee', 'payableSalary', 'paidAmount'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeSalaryRequest $request, $id): RedirectResponse
    {
        if (!checkAdminHasPermission('employee.pay.salary') || !checkAdminHasPermission('employee.pay.advance')) {
            abort(403);
        }
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
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        checkAdminHasPermissionAndThrowException('employee.edit.salary');
        $payment = EmployeeSalary::with('account')->find($id);
        $employee = $this->employee->find($payment->employee_id);
        $accounts = $this->purchaseService->getAccounts();
        return view('employee::salary.edit', compact('payment', 'employee', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('employee.edit.salary');
        try {
            $payment = EmployeeSalary::with('account')->find($id);
            $this->employee->updateSalary($request, $payment);
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.employee.index', [], ['messege' => 'Employee salary updated successfully', 'alert-type' => 'success']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.employee.index', [], ['messege' => $ex->getMessage(), 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('employee.delete.salary');
        $salary = EmployeeSalary::find($id);
        $salary->delete();
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.employee.index', [], ['messege' => 'Employee salary deleted successfully', 'alert-type' => 'success']);
    }

    public function salaryInfo(Request $request, $id)
    {
        $employee = $this->employee->find($id);

        $amount = $employee->getPaidAmountAttribute($request->month, $request->year);
        // $employee->getDueAmountAttribute($request->month, $request->year)
        // (,,,,) skipping destructuring
        [,,, $payableSalary] = $this->employee->calculateSalary($request, $id);
        return ['advanceAmount' => $amount, 'dueAmount' => $payableSalary - $amount, 'payableSalary' => $payableSalary];
    }

    public function salaryList()
    {
        checkAdminHasPermissionAndThrowException('employee.view.payment');
        $payments = EmployeeSalary::with('employee')->paginate(20);
        $payments->appends(request()->query());

        return view('employee::salary.salary-list', compact('payments'));
    }
}
