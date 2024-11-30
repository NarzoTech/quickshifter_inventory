<?php

namespace Modules\Supplier\app\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customer\app\Models\Area;
use Modules\Customer\app\Models\UserGroup;
use Modules\Purchase\app\Models\Purchase;
use Modules\Purchase\app\Models\PurchaseReturn;
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

    public function group()
    {
        return $this->belongsTo(UserGroup::class, 'group_id')->withDefault();
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id')->withDefault();
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class, 'supplier_id');
    }

    public function getAdvanceAttribute()
    {
        $payments = $this->payments;

        $totals = $payments->reduce(function ($carry, $payment) {
            if ($payment->payment_type === 'advance_pay') {
                $carry['total_advance'] += $payment->amount;
            } elseif ($payment->payment_type === 'advance_refund') {
                $carry['total_refund'] += $payment->amount;
            }
            return $carry;
        }, ['total_advance' => 0, 'total_refund' => 0]);

        return $totals['total_advance'] - $totals['total_refund'];
    }


    public function getTotalDueAttribute()
    {
        // return $this->total_purchase - $this->total_paid;
        return $this->purchases->sum('total_amount') - $this->payments->sum('amount');
    }

    public function duePurchase()
    {
        return $this->hasMany(Purchase::class, 'supplier_id')->where('payment_status', 'due');
    }

    public function purchaseReturn()
    {
        return $this->hasMany(PurchaseReturn::class);
    }
}
