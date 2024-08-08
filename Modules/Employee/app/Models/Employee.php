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
}
