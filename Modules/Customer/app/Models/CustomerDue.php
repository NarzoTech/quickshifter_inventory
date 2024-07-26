<?php

namespace Modules\Customer\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customer\Database\factories\CustomerDueFactory;

class CustomerDue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_dues';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'customer_id',
        'due_date',
        'due_amount',
        'status',
    ];

    protected static function newFactory(): CustomerDueFactory
    {
        //return CustomerDueFactory::new();
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id')->withDefault();
    }
}
