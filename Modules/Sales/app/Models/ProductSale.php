<?php

namespace Modules\Sales\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Sales\Database\factories\ProductSaleFactory;

class ProductSale extends Model
{
    use HasFactory;

    protected $table = 'product_sales';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sale_id',
        'product_id',
        'service_id',
        'quantity',
        'sale_unit_id',
        'product_sku',
        'variant_id',
        'attributes',
        'price',
        'tax',
        'discount',
        'sub_total',
    ];
}
