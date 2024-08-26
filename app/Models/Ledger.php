<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Supplier\app\Models\Supplier;
use Mollie\Api\Resources\Customer;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'supplier_id',
        'amount',
        'is_paid',
        'is_received',
        'due_amount',
        'invoice_type',
        'invoice_url',
        'invoice_no',
        'note',
        'date',
        'created_by',
        'updated_by',
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
}
