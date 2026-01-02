<?php

namespace Modules\Product\app\Models;

use App\Http\Resources\ProductResource;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Media\app\Models\Media;
use Modules\Order\app\Models\OrderDetails;
use Modules\Purchase\app\Models\PurchaseDetails;
use Modules\Purchase\app\Models\PurchaseReturnDetails;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\SalesReturnDetails;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'short_description',
        'brand_id',
        'category_id',
        'unit_id',
        'unit_sale_id',
        'unit_purchase_id',
        'image',
        'cost',
        'price',
        'stock_alert',
        'warranty',
        'is_imei',
        'not_selling',
        'stock',
        'stock_status',
        'sku',
        'barcode',
        'status',
        "tax_type",
        "tax",
        'is_favorite',
    ];

    public function getSingleImageAttribute()
    {
        $imageUrl = $this->getImagesUrlAttribute();

        // Skip invalid paths (temp files, null, empty)
        if (!$imageUrl || str_starts_with($imageUrl, '/tmp/') || str_starts_with($imageUrl, 'C:\\')) {
            return asset('backend/img/image_icon.png');
        }

        if (file_exists(public_path($imageUrl))) {
            return asset($imageUrl);
        }
        return asset('backend/img/image_icon.png');
    }

    protected $casts = [
        'images' => 'array',
        'attributes' => 'array',
    ];

    protected $appends = [
        'image_url',
        'stock_status',
        'has_variant',
        'total_stock',
        'current_price',
        'single_image',
    ];

    public function getCurrentPriceAttribute()
    {
        // check last purchase
        $purchase =  $this->latestPurchaseDetail;

        // get the selling price

        if ($purchase) {
            return remove_comma($purchase->sale_price);
        }

        return remove_comma($this->price);
    }

    public function getTotalPurchaseAttribute()
    {
        $purchase = $this->purchaseDetails;

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $purchase = $purchase->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $price = $purchase->sum('sub_total');
        $qty = $purchase->sum('quantity');

        return [
            'qty' => $qty ?? 0,
            'price' => $price ?? 0
        ];
    }


    public function getPurchasePriceAttribute()
    {
        $query = $this->purchaseDetails();

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $query = $query->whereHas('purchase', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('purchase_date', [$fromDate, $toDate]);
            });
        }

        $purchase = $query->selectRaw('SUM(purchase_price) as total_price, COUNT(*) as total_quantity')->first();

        $totalPrice = $purchase->total_price ?? 0;
        $totalQuantity = $purchase->total_quantity ?? 0;

        return $totalQuantity > 0 ? $totalPrice / $totalQuantity : 0;
    }




    public function getSalesAttribute()
    {
        $sales = $this->salesDetails;

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $sales = $sales->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $price = $sales->sum('sub_total');
        $qty = $sales->sum('quantity');
        return [
            'qty' => $qty ?? 0,
            'price' => $price ?? 0
        ];
    }




    public function getSalesReturnAttribute()
    {
        $sales = $this->salesReturnDetails;

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $sales = $sales->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $price = $sales->sum('sub_total');
        $qty = $sales->sum('quantity');

        return [
            'qty' => $qty ?? 0,
            'price' => $price ?? 0
        ];
    }



    public function getPurchaseReturnAttribute()
    {
        $purchase = $this->purchaseReturnDetails;

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $purchase = $purchase->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $price = $purchase->sum('total');
        $qty = $purchase->sum('quantity');

        return [
            'qty' => $qty ?? 0,
            'price' => $price ?? 0
        ];
    }


    public function getStockCountAttribute()
    {
        // If no dates provided, return total stock count (all time)
        if (!request('from_date') && !request('to_date')) {
            $totalInQty = $this->stockDetails->sum('in_quantity') ?? 0;
            $totalOutQty = $this->stockDetails->sum('out_quantity') ?? 0;
            return $totalInQty - $totalOutQty;
        }

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();

        // Get all stock data within the date range in a single query
        $stock = $this->stockDetails
            ->whereBetween('date', [$fromDate, $toDate]);
        $toDayInQty = $stock->sum('in_quantity') ?? 0;
        $toDayOutQty = $stock->sum('out_quantity') ?? 0;


        // Get previous stock quantities in a single query
        $previousStock = $this->stockDetails
            ->where('date', '<', $fromDate);


        // Calculate today stock
        $toDayStock = $toDayInQty - $toDayOutQty;

        $prevInQty = $previousStock->sum('in_quantity') ?? 0;
        $prevOutQty = $previousStock->sum('out_quantity') ?? 0;
        // Calculate total stock
        $previousStockTotal = $prevInQty - $prevOutQty;

        return $toDayStock + $previousStockTotal;
    }

    public function getAvgPurchasePriceAttribute()
    {
        $purchase = $this->purchaseDetails->sortByDesc('id');
        $totalPrice = $purchase->sum('purchase_price');
        $totalQuantity = $purchase->count();

        $price = $totalQuantity > 0 ? $totalPrice / $totalQuantity : 0;
        return (int) $price;
    }


    public function getCostAttribute()
    {
        $lastPurchase = $this->getLastPurchasePriceAttribute();

        return $lastPurchase > 0 ? $lastPurchase : $this->attributes['cost'];
    }



    public function getLastPurchasePriceAttribute()
    {
        $purchase = $this->latestPurchaseDetail;

        return $purchase ? $purchase->purchase_price : 0;
    }

    public function getSellingPriceAttribute()
    {
        $latestPurchase = $this->latestPurchaseDetail;
        return $latestPurchase ? $latestPurchase->sale_price : $this->attributes['price'];
    }

    public function latestPurchaseDetail(): HasOne
    {
        return $this->hasOne(PurchaseDetails::class, 'product_id', 'id')->latestOfMany();
    }

    public function stockDetails(): HasMany
    {
        return $this->hasMany(Stock::class, 'product_id', 'id');
    }

    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetails::class, 'product_id', 'id');
    }



    public function salesDetails(): HasMany
    {
        return $this->hasMany(ProductSale::class, 'product_id', 'id');
    }

    public function salesReturnDetails(): HasMany
    {
        return $this->hasMany(SalesReturnDetails::class, 'product_id', 'id');
    }

    public function purchaseReturnDetails(): HasMany
    {
        return $this->hasMany(PurchaseReturnDetails::class, 'product_id', 'id');
    }
    public function getHasVariantAttribute(): bool
    {
        return $this->variants_count > 0;
    }

    public function getActualPriceAttribute()
    {
        return $this->price;
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')->withDefault();
    }
    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id', 'id')->withDefault();
    }

    public function unit()
    {
        return $this->belongsTo(UnitType::class, 'unit_id', 'id')->withDefault();
    }

    public function getImagesAttribute($value)
    {
        return json_decode($value);
    }

    public function getImagesUrlAttribute()
    {
        $image = $this->image;
        if (gettype($image) == 'array') {
            return false;
        }

        return $image;
    }

    public function setImagesAttribute($value)
    {
        $this->attributes['images'] = json_encode($value);
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = json_encode($value);
    }


    public function getImageUrlAttribute()
    {
        $image = $this->image;

        // Skip invalid paths (temp files, null, empty)
        if (!$image || str_starts_with($image, '/tmp/') || str_starts_with($image, 'C:\\')) {
            return 'backend/img/image_icon.png';
        }

        return $image;
    }


    public function setAttributesAttribute($value)
    {
        $this->attributes['attributes'] = json_encode($value);
    }

    public function getPriceAttribute($value)
    {
        // Return the actual database value for the price column
        // This allows the price to be updated and retrieved correctly
        return $value;
    }

    public function getStockAttribute($value)
    {
        return number_format($value, 0);
    }

    public function orders()
    {
        return $this->hasMany(OrderDetails::class, 'product_id', 'id');
    }

    public function getRelatedProductAttribute()
    {
        return $this->relatedProducts->map(function ($relatedProduct) {
            return $relatedProduct->relatedProduct;
        });
    }

    public function getStockStatusAttribute($value)
    {
        return $value == 'in_stock' ? 'In Stock' : 'Out of Stock';
    }

    // variations section

    public function variants()
    {
        return $this->hasMany(Variant::class, 'product_id', 'id');
    }

    public function getAttributeAndValuesAttribute()
    {
        $attr = $this->variants->flatMap(function ($variant) {
            return $variant->options->map(function ($option) {
                return [
                    'attribute_id' => $option->attribute_id,
                    'attribute_value_id' => $option->attribute_value_id,
                    'attribute' => $option->attribute->name,
                    'attribute_value' => $option->attributeValue->name,
                ];
            });
        });

        $uniqueAttributes = $attr->unique('attribute')->values();

        $uniqueAttrWithValue = $uniqueAttributes->map(function ($uniqueAttr) use ($attr) {
            $values = $attr->filter(function ($item) use ($uniqueAttr) {
                return $item['attribute'] === $uniqueAttr['attribute'];
            })->map(function ($item) {
                return [
                    'id' => $item['attribute_value_id'],
                    'value' => $item['attribute_value']
                ];
            })->unique('id')->values()->toArray();

            return [
                'attribute_id' => $uniqueAttr['attribute_id'],
                'attribute' => $uniqueAttr['attribute'],
                'attribute_values' => $values,
            ];
        });

        return $uniqueAttrWithValue;
    }

    // get all variants price and sku with attribute value ids
    public function getVariantsPriceAndSkuAttribute()
    {
        $this->load('variants.variantOptions.attributeValue');

        $variantsPriceAndSku = [];

        foreach ($this->variants as $variant) {
            $variantsPriceAndSku[$variant->id] = [
                'price' => $variant->price,
                'currency_price' => currency($variant->price),
                'sku' => $variant->sku,
                'attribute_value_ids' => $variant->options->pluck('attribute_value_id')->toArray(),
            ];
        }

        return $variantsPriceAndSku;
    }

    public function getVariantsWithAttributes()
    {
        $this->load('variants.variantOptions.attributeValue.attribute');

        $variantsWithAttributes = [];

        foreach ($this->variants as $variant) {

            foreach ($variant->variantOptions as $variantOption) {
                $attributeValue = $variantOption->attributeValue;
                $attribute = $attributeValue->attribute;

                $variantsWithAttributes[$variant->id][] = [
                    'attribute' => $attribute->name,
                    'value' => $attributeValue->name,
                    'value_id' => $attributeValue->id,
                ];
            }
        }
        return $variantsWithAttributes;
    }

    public function getTotalStockAttribute()
    {
        return $this->attributes['stock'];
    }
}
