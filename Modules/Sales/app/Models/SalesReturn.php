<?php

namespace Modules\Sales\app\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Sales\Database\factories\SalesReturnFactory;

class SalesReturn extends Model
{
    use HasFactory;

    protected $table = 'sales_return';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sale_id',
        'customer_id',
        'order_date',
        'return_date',
        'return_amount',
        'note',
        'status',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'sale_return_id');
    }
}
