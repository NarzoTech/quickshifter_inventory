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
use Modules\Report\app\Models\OtherSummery;
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
            } elseif ($payment->payment_type === 'advance_deduct') {
                $carry['total_used'] += $payment->amount;
            }
            return $carry;
        }, ['total_advance' => 0, 'total_refund' => 0, 'total_used' => 0]);

        return $totals['total_advance'] - $totals['total_refund'] - $totals['total_used'];
    }

    /**
     * Get the true advance balance from ALL payments (ignores eager-load filters).
     * Use this when you need the actual current advance balance regardless of date filters.
     */
    public function getTrueAdvanceAttribute()
    {
        $payments = SupplierPayment::where('supplier_id', $this->id)
            ->whereIn('payment_type', ['advance_pay', 'advance_refund', 'advance_deduct'])
            ->get();

        $totals = $payments->reduce(function ($carry, $payment) {
            if ($payment->payment_type === 'advance_pay') {
                $carry['total_advance'] += $payment->amount;
            } elseif ($payment->payment_type === 'advance_refund') {
                $carry['total_refund'] += $payment->amount;
            } elseif ($payment->payment_type === 'advance_deduct') {
                $carry['total_used'] += $payment->amount;
            }
            return $carry;
        }, ['total_advance' => 0, 'total_refund' => 0, 'total_used' => 0]);

        return $totals['total_advance'] - $totals['total_refund'] - $totals['total_used'];
    }


    public function getTotalPurchaseAttribute()
    {
        return $this->purchases->sum('total_amount');
    }

    public function getTotalPaidAttribute()
    {
        // Count purchase-related payments only (not advance_pay — that's tracked separately in Advance column)
        // advance_deduct = when advance is consumed during a purchase
        $totalPaid = $this->payments
            ->whereIn('payment_type', ['purchase', 'due_pay', 'advance_deduct'])
            ->sum('amount');

        return $totalPaid;
    }

    public function getTotalDueAttribute()
    {
        $totalPurchase = $this->total_purchase;
        $totalPaid = $this->total_paid;

        // Only the UNPAID portion of purchase returns reduces what we owe
        // A fully received return is a wash (goods back + money back = no due change)
        $totalReturnDue = $this->purchaseReturn->sum(function ($r) {
            return $r->return_amount - $r->received_amount;
        });

        return $totalPurchase - $totalPaid - $totalReturnDue;
    }

    public function duePurchase()
    {
        return $this->hasMany(Purchase::class, 'supplier_id')->where('payment_status', 'due');
    }

    public function purchaseReturn()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function getTotalDueDismissAttribute()
    {
        return $this->otherSummery->sum('due');
    }

    public function otherSummery()
    {
        return $this->hasMany(OtherSummery::class);
    }
}
