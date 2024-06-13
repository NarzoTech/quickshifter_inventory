<?php

namespace Modules\Supplier\app\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customer\app\Models\Area;
use Modules\Customer\app\Models\UserGroup;
use Modules\Purchase\app\Models\Purchase;
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
        return $this->belongsTo(UserGroup::class, 'group_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'supplier_id');
    }

    public function getTotalPurchaseAttribute()
    {
        return $this->purchases->sum('total_amount');
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments->sum('amount');
    }

    public function getTotalDueAttribute()
    {
        return $this->total_purchase - $this->total_paid;
    }

}
