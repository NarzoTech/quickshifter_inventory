<?php

namespace Modules\Customer\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customer\Database\factories\CustomerGroupFactory;

class CustomerGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_groups';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'discount',
        'status',
        'created_by',
        'updated_by',
    ];
}
