<?php

namespace Modules\Employee\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'designation',
        'address',
        'join_date',
        'salary',
        'image',
        'status',
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset($this->image) : null;
    }
    public function employeeSalary()
    {
        return $this->hasMany(EmployeeSalary::class, 'employee_id', 'id');
    }
    public function getAdvanceAmountAttribute($month = null, $year = null)
    {
        $month = $month ?? now()->format('F');
        $year = $year ?? now()->format('Y');

        if ($this->employeeSalary->count()) {
            return $this->employeeSalary->where('type', 'advance')->where('month', $month)->where('year', $year)->sum('amount');
        } else {

            return 0;
        }
    }

    // due amount

    public function getDueAmountAttribute($month = null, $year = null)
    {
        $month = $month ?? now()->format('F');
        $year = $year ?? now()->format('Y');
        return $this->salary - $this->getAdvanceAmountAttribute($month, $year);
    }
}
