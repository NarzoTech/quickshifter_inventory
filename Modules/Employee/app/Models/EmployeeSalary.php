<?php

namespace Modules\Employee\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'month',
        'year',
        'salary',
        'payment_type',
        'account_id',
        'amount',
        'note',
    ];
}
