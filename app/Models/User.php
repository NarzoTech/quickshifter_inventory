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
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Customer\app\Models\UserGroup;
use Modules\Customer\app\Models\Vehicle;
use Modules\LiveChat\app\Models\Message;
use Modules\Order\app\Models\OrderReview;
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
        'plate_number',
        'guest',
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
        $totalSales = $this->sales->sum('grand_total');

        $due = $totalSales - $this->payment->sum('amount');
        return $due;
    }

    public function sales()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();

        // current route
        $route = request()->route()->getName();


        $sales = $this->hasMany(Sale::class, 'customer_id');

        if ($route == 'admin.report.customers') {

            return $sales->whereBetween('order_date', [$fromDate, $toDate]);
        }

        return $sales;
    }
    public function payment()
    {
        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();

        // current route
        $route = request()->route()->getName();
        $payment = $this->hasMany(CustomerPayment::class, 'customer_id');


        if ($route == 'admin.report.customers') {
            return $payment->whereBetween('payment_date', [$fromDate, $toDate]);
        }

        return $payment;
    }

    public function getTotalPaidAttribute()
    {
        $payment = $this->payment;
        return $payment->sum('amount');
    }

    public function advances()
    {
        $advance = $this->payment()->where('payment_type', 'advance_receive')->sum('amount');
        $advanceRefund = $this->payment()->where('payment_type', 'advance_refund')->sum('amount');
        return $advance - $advanceRefund;
    }

    public function orderReviews()
    {

        return $this->hasMany(OrderReview::class, 'user_id');
    }
}
