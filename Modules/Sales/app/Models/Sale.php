<?php

namespace Modules\Sales\app\Models;

use App\Models\Admin;
use App\Models\Payment;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Currency\app\Models\MultiCurrency;
use Modules\Sales\Database\factories\SaleFactory;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'customer_id',
        'warehouse_id',
        'quantity',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'payment_details',
        'order_discount',
        'total_tax',
        'grand_total',
        'notes',
        'invoice',
        'shipping_cost',
        'currency_id',
        'exchange_rate',
        'paid_amount',
        'sale_note',
        'staff_note',
        'order_date',
        'created_by',
    ];
    public function details()
    {
        return $this->hasMany(ProductSale::class, 'sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id')->withDefault();
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id')->withDefault();
    }

    public function currency()
    {
        return $this->belongsTo(MultiCurrency::class, 'currency_id', 'id')->withDefault();
    }

    public function products()
    {
        return $this->hasMany(ProductSale::class, 'sale_id')->where('service_id', null);
    }

    public function services()
    {
        return $this->hasMany(ProductSale::class, 'sale_id')->where('product_id', null);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class, 'sale_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by')->withDefault();
    }
}
