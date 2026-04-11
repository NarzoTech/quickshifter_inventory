<?php

namespace Modules\Employee\app\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryIncrement extends Model
{
    protected $fillable = [
        'employee_id',
        'previous_salary',
        'new_salary',
        'increment_type',
        'increment_value',
        'note',
        'incremented_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withDefault();
    }
}
