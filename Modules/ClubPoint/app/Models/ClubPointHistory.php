<?php

namespace Modules\ClubPoint\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ClubPoint\Database\factories\ClubPointHistoryFactory;
use Modules\Order\app\Models\Order;

class ClubPointHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): ClubPointHistoryFactory
    {
        //return ClubPointHistoryFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->select('id', 'name', 'email', 'image');
    }

    public function order()
    {
        return $this->belongsTo(Order::class)->select('id', 'order_id', 'order_status', 'payment_status');
    }
}
