<?php

namespace Modules\Customer\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Customer\Database\factories\CustomerPaymentFactory;

class CustomerPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sale_id',
        'customer_id',
        'account_id',
        'invoice',
        'is_guest',
        'is_received',
        'is_paid',
        'payment_type',
        'account_type',
        'amount',
        'payment_date',
        'note',
        'created_by',
        'updated_by',
    ];
}
