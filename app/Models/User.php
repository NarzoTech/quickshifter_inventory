<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Customer\app\Models\Area;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Customer\app\Models\UserGroup;
use Modules\Customer\app\Models\Vehicle;
use Modules\LiveChat\app\Models\Message;
use Modules\Sales\app\Models\Sale;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'group_id',
        'area_id',
        'vehicle_id',
        'membership',
        'date',
        'address',
        'status',
        'wallet_balance',
    ];

    protected $appends = ['total_due'];

    public function group()
    {
        return $this->belongsTo(UserGroup::class, 'group_id');
    }
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id')->withDefault();
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function due()
    {
        return $this->hasMany(CustomerDue::class, 'customer_id');
    }
    public function getTotalDueAttribute()
    {
        $due = $this->due()->where('status', 1)->sum('due_amount');
        return $due;
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'customer_id');
    }
    public function payment()
    {
        return $this->hasMany(Payment::class, 'customer_id');
    }
    public function getTotalPaidAttribute()
    {
        return $this->payment->sum('amount');
    }
}
