<?php

namespace Modules\Expense\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Expense\Database\factories\ExpenseFactory;

class Expense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'date',
        'payment_type',
        'account_id',
        'account_details',
        'payment_status',
        'note',
        'amount',
        'expense_type_id',
        'expense_type_id',
        'created_by',
        'updated_by',
    ];
}
