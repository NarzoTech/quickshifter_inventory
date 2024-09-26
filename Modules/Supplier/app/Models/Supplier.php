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
        $from_date = null;
        $to_date = null;
        if (request()->from_date) {
            $from_date = now()->parse(request()->from_date);
        }
        if (request()->to_date) {
            $to_date = now()->parse(request()->to_date);
        }

        $advance = $this->payments()->where('payment_type', 'advance_pay');

        if ($from_date || $to_date) {
            $advance = $advance->whereBetween('date', [$from_date, $to_date]);
        }
        $advance = $advance->sum('amount');
        $advance_return = $this->payments()->where('payment_type', 'advance_refund')->sum('amount');
        return $advance - $advance_return;
    }

    public function getTotalPurchaseAttribute()
    {
        $from_date = null;
        $to_date = null;
        if (request()->from_date) {
            $from_date = now()->parse(request()->from_date);
        }
        if (request()->to_date) {
            $to_date = now()->parse(request()->to_date);
        }

        $purchases = $this->purchases;

        if ($from_date || $to_date) {
            $purchases = $purchases->whereBetween('date', [$from_date, $to_date]);
        }

        return $purchases->sum('total_amount');
    }

    public function getTotalPaidAttribute()
    {
        $from_date = null;
        $to_date = null;
        if (request()->from_date) {
            $from_date = now()->parse(request()->from_date);
        }
        if (request()->to_date) {
            $to_date = now()->parse(request()->to_date);
        }

        $payments = $this->payments->where('is_paid', 1);
        if ($from_date || $to_date) {
            $payments = $payments->whereBetween('date', [$from_date, $to_date]);
        }

        return $payments->sum('amount');
    }

    public function getTotalDueAttribute()
    {
        return $this->total_purchase - $this->total_paid;
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
