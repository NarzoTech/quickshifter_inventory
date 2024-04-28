<?php

namespace Modules\Subscription\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Subscription\Database\factories\SubscriptionHistoryFactory;

class SubscriptionHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): SubscriptionHistoryFactory
    {

    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
