<?php

namespace Modules\Employee\app\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Accounts\app\Models\Account;
use Modules\Attendance\app\Models\HolidaySetup;
use Modules\Attendance\app\Models\WeekendSetup;
use Modules\Employee\app\Models\Employee;
use Modules\Employee\app\Models\EmployeeSalary;

class EmployeeService
{
    public function __construct(private Employee $employee) {}

    public function all()
    {
        return $this->employee->with(['employeeSalary', 'attendance', 'currentSalary']);
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
        $data['payable_salary'] = $request->payable_salary;
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


    public function calculateSalary(Request $request, $id)
    {

        // check if weekend days is in cache
        if (!Cache::has('weekends')) {
            $weekends = WeekendSetup::where('is_weekend', 1)->pluck('name')->toArray();
            Cache::put('weekends', $weekends);
        }

        // get the  weekend days
        $weekends = cache('weekends');

        $employee = $this->employee->find($id);
        $month = $request->month ?? now()->format('F');
        $monthNumber = now()->parse($month)->month;
        $year = $request->year ?? now()->format('Y');


        $payments = EmployeeSalary::with(['employee', 'account'])->where('employee_id', $id)->where('month', $month)->where('year', $year)
            ->get();


        // total attendance of employee in that month
        $totalAttendance = $employee->attendance()->whereMonth('date', $monthNumber)->whereYear('date', $year)->count();



        $weekendDays = collect($weekends)->map(function ($day) {
            return now()->parse($day)->dayOfWeek;
        })->toArray();



        $totalWeekends = 0;

        $startOfMonth = now()->month($monthNumber)->year($year)->startOfMonth();
        $endOfMonth = now()->month($monthNumber)->year($year)->endOfMonth();
        $currentDate = now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;
        $searchMonth = $startOfMonth->month;
        $searchYear = $startOfMonth->year;


        if ($searchYear <= $currentYear && ($searchYear <= $currentYear && $searchMonth <= $currentMonth)) {
            $totalDaysOfTheMonth = $endOfMonth;
            if ($endOfMonth->month == $currentMonth) {
                $currentDay = $currentDate->day;
                $endDay =  $endOfMonth->day;
                $totalDaysOfTheMonth = $totalDaysOfTheMonth->subDays($endDay - $currentDay);
            }
            for ($date = $startOfMonth; $date <= $totalDaysOfTheMonth; $date->addDay()) {
                if (in_array($date->dayOfWeek, $weekendDays)) {
                    $totalWeekends++;
                }
            }
        } else {
            $totalWeekends = 0;
        }
        $holidays = HolidaySetup::where(function ($query) use ($monthNumber, $year) {
            $query->whereMonth('start_date', $monthNumber)
                ->whereYear('start_date', $year);
        })->get();

        // count total holidays
        $totalHolidays = 0;

        foreach ($holidays as $holiday) {
            // defecrence between start date and end date
            $difference = now()->parse($holiday->end_date)->diffInDays($holiday->start_date);

            for ($date = now()->parse($holiday->start_date); $date <= now()->parse($holiday->end_date); $date->addDay()) {
                if (in_array($date->dayOfWeek, $weekendDays)) {
                    $difference--;
                }
            }

            $totalHolidays += $difference + 1;
        }

        // current month total days
        $totalDays = now()->month($monthNumber)->year($year)->daysInMonth;

        $totalWorkingDays = $totalDays - ($totalWeekends + $totalHolidays);

        $totalDayOff = $totalWeekends + $totalHolidays;

        $payableSalary = $employee->salary;
        if ($totalWorkingDays != $totalAttendance) {
            $payableSalary = ($payableSalary / $totalDays) * ($totalWeekends + $totalHolidays + $totalAttendance);
        }
        $payableSalary = (int) $payableSalary;

        return [$payments, $employee, $month, $payableSalary, $totalAttendance, $totalDayOff];
    }
}
