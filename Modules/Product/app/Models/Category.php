<?php

namespace Modules\Product\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Media\app\Models\Media;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'parent_id',
        'status',
        'name'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    // calculate query time and total query

    public function calcQuery()
    {
        $this->products->get();
        $this->products->count();
    }


    protected function ensureProductsLoaded()
    {
        if (!$this->relationLoaded('products')) {
            $this->load('products');
        }
    }

    public function getPurchaseSummaryAttribute()
    {
        $this->ensureProductsLoaded();
        // Initialize count and amount
        $count = $this->products->sum(function ($product) {
            return $product->total_purchase['qty'] ?? 0; // Handle null values
        });

        $amount = $this->products->sum(function ($product) {
            return $product->total_purchase['price'] ?? 0; // Handle null values
        });

        // Return as an array
        return [
            'count' => $count,
            'amount' => $amount,
        ];
    }

    public function getSalesCountAttribute()
    {
        $this->ensureProductsLoaded();
        $count = 0;
        foreach ($this->products as $product) {
            // Only count sales from own inventory (source = 1)
            $ownSalesQuery = \Modules\Sales\app\Models\ProductSale::where('product_id', $product->id)
                ->where('source', 1);
            
            $ownSalesReturnsQuery = \Modules\Sales\app\Models\SalesReturnDetails::where('product_id', $product->id)
                ->where('source', 1);
            
            // Apply date filters if provided
            if (request('from_date') || request('to_date')) {
                $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
                $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
                
                $ownSalesQuery->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                    $q->whereBetween('order_date', [$fromDate, $toDate]);
                });
                
                $ownSalesReturnsQuery->whereHas('saleReturn', function ($q) use ($fromDate, $toDate) {
                    $q->whereBetween('created_at', [$fromDate, $toDate]);
                });
            }
            
            $saleQty = $ownSalesQuery->sum('quantity');
            $returnQty = $ownSalesReturnsQuery->sum('quantity');
            
            $count += ($saleQty - $returnQty);
        }

        return $count;
    }


    public function getSalesAmountAttribute()
    {
        $this->ensureProductsLoaded();
        $amount = 0;
        foreach ($this->products as $product) {
            // Only count sales from own inventory (source = 1)
            $ownSalesQuery = \Modules\Sales\app\Models\ProductSale::where('product_id', $product->id)
                ->where('source', 1);
            
            $ownSalesReturnsQuery = \Modules\Sales\app\Models\SalesReturnDetails::where('product_id', $product->id)
                ->where('source', 1);
            
            // Apply date filters if provided
            if (request('from_date') || request('to_date')) {
                $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
                $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
                
                $ownSalesQuery->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                    $q->whereBetween('order_date', [$fromDate, $toDate]);
                });
                
                $ownSalesReturnsQuery->whereHas('saleReturn', function ($q) use ($fromDate, $toDate) {
                    $q->whereBetween('created_at', [$fromDate, $toDate]);
                });
            }
            
            $saleAmount = $ownSalesQuery->sum('sub_total');
            $returnAmount = $ownSalesReturnsQuery->sum('sub_total');
            
            $amount += ($saleAmount - $returnAmount);
        }

        return $amount;
    }


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id')->withDefault();
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
