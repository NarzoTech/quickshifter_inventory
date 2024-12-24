<?php

namespace Modules\Customer\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Customer\Database\factories\VehicleFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'model',
        'plate_number',
        'color',
        'year',
    ];
}
