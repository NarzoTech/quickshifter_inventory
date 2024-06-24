<?php

namespace Modules\Purchase\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\app\Models\Product;
use Modules\Purchase\Database\factories\PurchaseReturnDetailsFactory;

class PurchaseReturnDetails extends Model
{
    use HasFactory;

    protected $table = 'purchase_return_details';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'purchase_return_id',
        'product_id',
        'quantity',
        'total',
    ];

    // relationship
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
