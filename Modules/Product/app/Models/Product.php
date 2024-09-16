<?php

namespace Modules\Product\app\Models;

use App\Http\Resources\ProductResource;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Media\app\Models\Media;
use Modules\Order\app\Models\OrderDetails;
use Modules\Purchase\app\Models\PurchaseDetails;
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
        'images',
        'cost',
        'price',
        'stock_alert',
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

        $imageUrls =  $this->getImagesUrlAttribute();
        if ($imageUrls) {
            return $imageUrls[0];
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
    ];

    public function getCurrentPriceAttribute()
    {
        // check last purchase
        $purchase = PurchaseDetails::where('product_id', $this->id)->orderBy('id', 'desc')->first();

        // get the selling price

        if ($purchase) {
            return remove_comma($purchase->sale_price);
        }

        return remove_comma($this->price);
    }

    public function getTotalPurchaseAttribute()
    {
        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        $purchase = $this->purchaseDetails()->where(function ($q) use ($fromDate, $toDate) {
            $q->whereHas('purchase', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('purchase_date', [$fromDate, $toDate]);
            });
        });

        return ['qty' => $purchase->sum('quantity'), 'price' => $purchase->sum('sub_total')];
    }

    public function getSalesAttribute()
    {
        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();

        $sales = $this->salesDetails()->where(function ($q) use ($fromDate, $toDate) {
            $q->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('order_date', [$fromDate, $toDate]);
            });
        })->where('source', 1);

        return ['qty' => $sales->sum('quantity'), 'price' => $sales->sum('sub_total')];
    }

    public function getSalesReturnAttribute()
    {
        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();

        $sales = $this->salesReturnDetails()->where(function ($q) use ($fromDate, $toDate) {
            $q->whereHas('saleReturn', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('return_date', [$fromDate, $toDate]);
            });
        })->where('source', 1);

        return ['qty' => $sales->sum('quantity'), 'price' => $sales->sum('sub_total')];
    }

    public function getAvgPurchasePriceAttribute()
    {
        $purchase = $this->purchaseDetails()->orderBy('id', 'desc')->get();
        $totalPrice = $purchase->sum('purchase_price');
        $totalQuantity = $purchase->count();

        return $totalQuantity > 0 ? $totalPrice / $totalQuantity : 0;
    }

    public function getLastPurchasePriceAttribute()
    {
        $purchase = $this->purchaseDetails()->orderBy('id', 'desc')->get();

        return $purchase->count() > 0 ? $purchase->first()->purchase_price : 0;
    }

    public function getSellingPriceAttribute()
    {
        $purchase = $this->purchaseDetails()->orderBy('id', 'desc')->first();

        return $purchase ? $purchase->sale_price : $this->price;
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
    public function getHasVariantAttribute(): bool
    {
        return $this->variants->count() > 0;
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
        $images = $this->images;
        if ($images) {
            $images = explode(',', $images[0]);

            $media = Media::whereIn('id', $images)->select('path')->get()->toArray();

            // flatten the array
            $media = array_map(function ($item) {
                return asset($item['path']);
            }, $media);

            return $media;
        }
        return [];
    }

    public function setImagesAttribute($value)
    {
        $this->attributes['images'] = json_encode($value);
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = json_encode($value);
    }

    public function mediaImage()
    {
        return $this->belongsTo(Media::class, 'image', 'id');
    }

    public function getImageUrlAttribute()
    {
        return $this->mediaImage?->path;
    }


    public function setAttributesAttribute($value)
    {
        $this->attributes['attributes'] = json_encode($value);
    }

    public function getPriceAttribute($value)
    {
        return number_format($value, 2);
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
