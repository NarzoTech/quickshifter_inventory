<?php

namespace Modules\Order\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Order\Database\factories\OrderFactory;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'walk_in_customer',
        'address_id',
        'delivery_fee',
        'tax',
        'discount',
        'order_delivery_date',
        'payment_details',
        'payment_notes',
        'order_note',
        'total_amount',
        'order_id',
        'transaction_id',
        'payment_method',
        'created_by',
        'payment_status',
        'order_status',
        'delivery_method',
        'delivery_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->select('id', 'name', 'email', 'image');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }
    public function getQuantityAttribute(){
        return $this->orderDetails->sum('quantity');
    }
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function getAmountAttribute(){
        $total = $this->total_amount * $this->currency_rate;

        return $this->currency_icon. $total;
    }
}
