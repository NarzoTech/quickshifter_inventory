<?php

namespace Modules\Accounts\app\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Accounts\Database\factories\AccountFactory;

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
        return $receive - $paid;
    }
}
