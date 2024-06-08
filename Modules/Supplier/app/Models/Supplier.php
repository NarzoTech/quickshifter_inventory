<?php

namespace Modules\Supplier\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Supplier\Database\factories\SupplierFactory;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;


    protected $table = 'supplier';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'company',
        'phone',
        'email',
        'group_id',
        'area_id',
        'date',
        'address',
        'status',
        'guest',
    ];
}
