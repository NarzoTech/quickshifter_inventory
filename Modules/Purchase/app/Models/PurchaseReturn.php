<?php

namespace Modules\Purchase\app\Models;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Purchase\Database\factories\PurchaseReturnFactory;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $table = 'purchase_returns';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'purchase_id',
        'return_type_id',
        'warehouse_id',
        'return_date',
        'note',
        'payment_method',
        'received_amount',
        'return_amount',
        'payment_status',
        'shipping_cost',
    ];

    // relationships
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function returnType()
    {
        return $this->belongsTo(PurchaseReturnType::class, 'return_type_id','id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseReturnDetails::class);
    }
}
