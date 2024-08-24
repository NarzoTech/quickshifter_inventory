<?php

namespace Modules\Supplier\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Supplier\Database\factories\SupplierPaymentFactory;

class SupplierPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'purchase_id',
        'invoice',
        'supplier_id',
        'account_id',
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

    protected static function newFactory(): SupplierPaymentFactory
    {
        //return SupplierPaymentFactory::new();
    }
}
