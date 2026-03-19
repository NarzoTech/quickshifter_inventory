<?php

namespace Modules\Employee\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Attendance\app\Models\Attendance;
use Modules\Employee\Database\factories\EmployeeFactory;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'nid',
        'guardian_name',
        'guardian_mobile',
        'guardian_relation',
        'designation',
        'address',
        'join_date',
        'salary',
        'yearly_leaves',
        'image',
        'status',
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset($this->image) : null;
    }
    public function employeeSalary()
    {
        $month = request('month');

        $month = ($month != null && $month != '' && $month != '0') ? now()->parse($month)->format('F') : now()->format('F');

        // dd($month);
        return $this->hasMany(EmployeeSalary::class, 'employee_id', 'id')->where('month', $month)->where('year', request('year'));
    }

    public function currentSalary()
    {
        return $this->hasMany(EmployeeSalary::class, 'employee_id', 'id');
    }
    public function getAdvanceAmountAttribute($month = null, $year = null)
    {
        $month = $month ?? now()->format('F');
        $year = $year ?? now()->format('Y');

        return $this->currentSalary
            ->where('type', 'advance')
            ->where('month', $month)
            ->where('year', $year)
            ->sum('amount');
    }

    /**
     * Calculate carry-forward amount from all previous months.
     * If total paid in previous months exceeds total payable, the excess carries forward.
     */
    public function getCarryForwardAttribute($month = null, $year = null)
    {
        $month = $month ?? now()->format('F');
        $year = $year ?? now()->format('Y');

        $targetDate = \Carbon\Carbon::parse("1 {$month} {$year}");

        // Get all salary records before this month
        $previousRecords = $this->currentSalary()
            ->get()
            ->filter(function ($record) use ($targetDate) {
                $recordDate = \Carbon\Carbon::parse("1 {$record->month} {$record->year}");
                return $recordDate->lt($targetDate);
            });

        if ($previousRecords->isEmpty()) {
            return 0;
        }

        // Group by month-year
        $grouped = $previousRecords->groupBy(function ($record) {
            return $record->year . '-' . $record->month;
        });

        $totalPaidAllPrevious = 0;
        $totalMonths = $grouped->count();

        foreach ($grouped as $key => $records) {
            $totalPaidAllPrevious += $records->sum('amount');
        }

        // Compare against base salary (not attendance-based payable)
        // Carry-forward only happens when employee is paid MORE than their monthly salary
        // e.g., salary=10000, paid=11000 in March → 1000 carries to April
        $totalPayableAllPrevious = $this->salary * $totalMonths;

        // Only carry forward overpayments (positive excess)
        $excess = $totalPaidAllPrevious - $totalPayableAllPrevious;
        return max(0, $excess);
    }

    // due amount

    public function getDueAmountAttribute($month = null, $year = null)
    {
        $month = $month ?? now()->format('F');
        $year = $year ?? now()->format('Y');
        return $this->salary - $this->getPaidAmountAttribute($month, $year);
    }

    public function getPaidAmountAttribute($month = null, $year = null)
    {
        $month = $month ?? now()->format('F');
        $year = $year ?? now()->format('Y');


        return $this->currentSalary->where('month', $month)->where('year', $year)->sum('amount');
    }

    public function attendance()
    {
        $month_year = request()->month_year ?? now()->format('m/Y');

        $date = \Carbon\Carbon::createFromFormat('m/Y', $month_year);

        $month = $date->month;
        $year = $date->year;


        return $this->hasMany(Attendance::class, 'employee_id', 'id')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where(function ($query) {
                $query->where('status', 'present')->orWhere('status', 'weekend');
            });
    }
}
