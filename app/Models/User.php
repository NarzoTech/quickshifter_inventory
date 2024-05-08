<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\LiveChat\app\Models\Message;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'tax_number',
        'address',
        'city',
        'status',
        'wallet_balance',
    ];

}
