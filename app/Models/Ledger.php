<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'supplier_id',
        'opening_balance',
        'closing_balance',
        'debit_amount',
        'credit_amount',
        'amount',
        'invoice_type',
        'invoice_url',
        'invoice_no',
        'note',
        'date',
        'created_by',
        'updated_by',
    ];
}
