<?php

namespace Modules\Accounts\app\Models;

use App\Models\Asset;
use App\Models\Balance;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Accounts\Database\factories\AccountFactory;
use Modules\Expense\app\Models\Expense;

class Account extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'account_type',
        'mobile_bank_name',
        'mobile_number',
        'bank_id',
        'service_charge',
        'card_type',
        'card_holder_name',
        'card_number',
        'bank_account_type',
        'bank_account_name',
        'bank_account_number',
        'bank_account_branch',
    ];

    /**
     * The attributes that should be cast to native types.
     */

    public function bank()
    {
        return $this->belongsTo(Bank::class)->withDefault();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function balance()
    {
        $receive =  $this->payments()->where('is_received', 1)->sum('amount');
        $paid = $this->payments()->where('is_paid', 1)->sum('amount');
        $deposit = $this->deposits()->sum('amount');
        $withdraw = $this->withdraws()->sum('amount');
        $asset = $this->assets()->sum('amount');
        $expenses = $this->expenses->sum('amount');
        $balance = ($receive + $deposit) - ($paid  + $withdraw + $asset + $expenses);
        return $balance;
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'account_id');
    }

    public function deposits()
    {
        return $this->hasMany(Balance::class, 'account_id')->where('balance_type', 'deposit');
    }

    public function withdraws()
    {
        return $this->hasMany(Balance::class, 'account_id')->where('balance_type', 'withdraw');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'account_id');
    }
}
