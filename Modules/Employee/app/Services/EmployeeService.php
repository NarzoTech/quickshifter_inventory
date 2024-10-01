<?php

namespace Modules\Employee\app\Services;

use Modules\Accounts\app\Models\Account;
use Modules\Employee\app\Models\Employee;
use Modules\Employee\app\Models\EmployeeSalary;

class EmployeeService
{
    public function __construct(private Employee $employee) {}

    public function all()
    {
        return $this->employee->with('employeeSalary');
    }

    public function find($id)
    {
        return $this->all()->find($id);
    }
    public function store(array $data)
    {
        $this->employee->create($data);
    }

    public function update($id, array $data)
    {
        $this->employee->find($id)->update($data);
    }

    public function destroy($id)
    {
        $this->employee->find($id)->delete();
    }

    public function changeStatus($id)
    {
        $employee = $this->employee->find($id);
        $employee->update(['status' => $employee->status == 1 ? 0 : 1]);
    }

    public function addSalary($request, $employee)
    {

        $data = $request->except('_token');
        $data['employee_id'] = $employee->id;
        $data['date'] = now()->parse($request->date);
        $data['month'] = now()->parse($request->month)->format('F');
        $data['year'] = now()->parse($request->date)->format('Y');
        $data['type'] = isset($request->type) && $request->type == 2 ? 'advance' : 'salary';
        $data['salary'] = $request->salary;
        $data['payment_type'] = $request->payment_type;
        $data['amount'] = $request->amount;
        $data['note'] = $request->note;

        $account = Account::where('account_type', $request->payment_type);
        if (
            $request->payment_type == 'cash'
        ) {
            $account = $account->first();
        } else {
            $account = $account->where('id', $request->account_id)->first();
        }
        $data['account_id'] = $account->id;
        $employee->employeeSalary()->create($data);
    }


    public function updateSalary($request, $payment)
    {

        $data = $request->except('_token');
        $data['date'] = now()->parse($request->date);
        $data['month'] = now()->parse($request->date)->format('F');
        $data['year'] = now()->parse($request->date)->format('Y');
        $data['payment_type'] = $request->payment_type;
        $data['amount'] = $request->amount;
        $data['note'] = $request->note;
        $data['account_id'] = $payment->account_id;

        $account = Account::where('account_type', $request->payment_type);
        if (
            $request->payment_type == 'cash'
        ) {
            $account = $account->first();
        } else {
            $account = $account->where('id', $request->account_id)->first();
        }
        $data['account_id'] = $account->id;

        $payment->update($data);

        return back()->with(['messege' => 'Salary updated successfully', 'alert-type' => 'success']);
    }
}
