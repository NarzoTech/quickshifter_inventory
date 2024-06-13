<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounts\app\Models\Account;
use Modules\Purchase\app\Models\Purchase;
use Modules\Supplier\app\Models\Supplier;
use Mollie\Api\Resources\Customer;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'purchase_id',
        'sale_id',
        'supplier_id',
        'customer_id',
        'account_id',
        'payment_type',
        'amount',
        'payment_date',
        'note',
        'created_by',
        'updated_by',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
}
