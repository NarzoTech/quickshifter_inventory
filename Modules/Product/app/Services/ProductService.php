<?php

namespace Modules\Product\app\Services;

use App\Models\Stock;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\app\Models\Product;
use Modules\Product\app\Models\Variant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Product\app\Models\VariantOption;
use Modules\Product\app\Models\AttributeValue;
use Modules\Language\app\Enums\TranslationModels;
use Modules\Language\app\Traits\GenerateTranslationTrait;
use Modules\Product\app\Models\Category;
use Modules\Product\app\Models\ProductBrand;
use Modules\Product\app\Models\UnitType;

class ProductService
{
    use GenerateTranslationTrait;
    protected Product $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }
    public function getProducts()
    {
        $query = $this->product->with(['salesDetails', 'salesReturnDetails', 'purchaseReturnDetails', 'stockDetails', 'orders', 'brand', 'purchaseDetails.purchase', 'latestPurchaseDetail', 'unit', 'category', 'brand']);

        if (request('keyword')) {
            $query = $query->where(function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%')
                    ->orWhere('sku', 'like', '%' . request()->keyword . '%')
                    ->orWhere('barcode', 'like', '%' . request()->keyword . '%');
            });
        }
        if (request('order_by')) {
            $query = $query->orderBy('name', request('order_by'));
        } else {
            $query = $query->orderBy('name', 'asc');
        }
        if (request('brand_id')) {
            $query = $query->where('brand_id', request('brand_id'));
        }
        if (request('category_id')) {
            $query = $query->where('category_id', request('category_id'));
        }
        return $query;
    }

    // get all active products
    public function allActiveProducts($request)
    {
        $products = $this->product->where('status', 1)->withCount('variants')->with('category', 'purchaseDetails', 'latestPurchaseDetail');

        $sort = request()->order_by ? request()->order_by : 'asc';
        $products = $products->orderBy('name', $sort);
        return $products;
    }

    public function getProduct($id): ?Product
    {
        return $this->product->with('stockDetails')->where('id', $id)->first();
    }
    public function storeProduct($request)
    {

        $data = $request->validated();

        if ($request->file('image')) {
            $data['image'] = file_upload($request->image);
        }


        $product = $this->product->create(
            $data
        );
        Stock::create([
            'purchase_id' => null,
            'product_id' => $product->id,
            'date' => now(),
            'type' => '	Opening Stock',
            'in_quantity' => $request->stock,
            'available_qty' => $request->stock,
            'sku' => $request->sku,
            'purchase_price' => $request->cost,
            'rate' => $request->cost,
            'sale_price' => $request->price,
            'tax' => $request->tax,
            'created_by' => auth('admin')->user()->id,
        ]);

        return $product;
    }

    public function updateProduct($request, $product)
    {
        $data = $request->validated();
        if ($request->file('image')) {
            $data['image'] = file_upload($request->image);
        }

        $product->update(
            $data
        );

        // Update stock if stock value is provided and changed
        if ($request->has('stock') && $request->stock != $product->stock) {
            $stockDifference = $request->stock - $product->getOriginal('stock');
            
            // Create stock adjustment entry
            Stock::create([
                'purchase_id' => null,
                'product_id' => $product->id,
                'date' => now(),
                'type' => $stockDifference > 0 ? 'Stock Adjustment (Add)' : 'Stock Adjustment (Subtract)',
                'in_quantity' => $stockDifference > 0 ? $stockDifference : 0,
                'out_quantity' => $stockDifference < 0 ? abs($stockDifference) : 0,
                'available_qty' => $request->stock,
                'sku' => $product->sku,
                'purchase_price' => $product->cost ?? 0,
                'rate' => $product->cost ?? 0,
                'sale_price' => $product->price ?? 0,
                'tax' => $product->tax ?? 0,
                'created_by' => auth('admin')->user()->id,
            ]);
        }

        return $product;
    }

    public function getActiveProductById($id)
    {
        return $this->product->where('id', $id)->where('status', 1)->first();
    }

    public function deleteProduct($product)
    {
        // check if product has orders
        if ($product->orders->count() > 0) {
            return false;
        }
        return $product->delete();
    }
    public function bulkDelete($ids)
    {
        foreach ($ids as $id) {
            $this->deleteProduct($this->getActiveProductById($id));
        }
        return true;
    }

    public function storeRelatedProducts($request, $product)
    {
        $ids = $request->product_id;


        // Remove existing related products
        $product->relatedProducts()->delete();

        // Add new related products
        foreach ($ids as $relatedProductId) {
            $product->relatedProducts()->create([
                'related_product_id' => $relatedProductId
            ]);
        }

        return $product;
    }
    public function getProductBySlug($slug): ?Product
    {
        return $this->product->where('slug', $slug)->first();
    }

    public function getProductsByCategory($category_id, $limit = 10): Collection
    {
        return $this->product->where('category_id', $category_id)->limit($limit)->get();
    }

    public function getProductsByBrand($brand_id, $limit = 10): Collection
    {
        return $this->product->where('brand_id', $brand_id)->limit($limit)->get();
    }

    public function getProductsByTag($tag, $limit = 10): Collection
    {
        return $this->product->where('tags', 'like', '%' . $tag . '%')->limit($limit)->get();
    }

    public function getFeaturedProducts($limit = 10): Collection
    {
        return $this->product->where('is_featured', 1)->limit($limit)->get();
    }

    public function getBestSellingProducts($limit = 10): Collection
    {
        return $this->product->where('is_best_selling', 1)->limit($limit)->get();
    }

    public function getTopRatedProducts($limit = 10): Collection
    {
        return $this->product->where('is_top_rated', 1)->limit($limit)->get();
    }

    public function getNewArrivalProducts($limit = 10): Collection
    {
        return $this->product->where('is_new_arrival', 1)->limit($limit)->get();
    }

    public function getRelatedProducts($product)
    {
        return $product->relatedProducts->pluck('related_product_id')->toArray();
    }

    public function getProductsBySearch($search, $limit = 10): Collection
    {
        return $this->product->where('name', 'like', '%' . $search . '%')->limit($limit)->get();
    }

    public function getProductsByPriceRange($min, $max, $limit = 10): Collection
    {
        return $this->product->whereBetween('price', [$min, $max])->limit($limit)->get();
    }

    public function getProductsByDiscount($limit = 10): Collection
    {
        return $this->product->where('discount', '>', 0)->limit($limit)->get();
    }

    public function getProductsByAttribute($attribute, $limit = 10): Collection
    {
        return $this->product->where('attributes', 'like', '%' . $attribute . '%')->limit($limit)->get();
    }

    public function getVariantBySku($sku)
    {
        return Variant::where('sku', $sku)->first();
    }

    public function getProductVariants($product)
    {
        $variants = $product->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'cost' => $variant->cost,
                'attribute' => $variant->attributes(),
                'attributes' => $variant->options->map(function ($option) {
                    return [
                        'attribute_id' => $option->attribute_id,
                        'attribute_value_id' => $option->attribute_value_id,
                        'attribute' => $option->attribute->name,
                        'attribute_value' => $option->attributeValue->name,
                    ];
                }),
            ];
        });

        return $variants;
    }

    public function getProductAttributesByVariant($product)
    {
        $variants = $product->variants->map(function ($variant) {
            return $variant->options->map(function ($option) {
                return [
                    'attribute_id' => $option->attribute_id,
                    'attribute_value_id' => $option->attribute_value_id,
                    'attribute' => $option->attribute->name,
                    'attribute_value' => $option->attributeValue->name,
                ];
            });
        });

        return $variants;
    }

    public function getProductAttributeValuesIds($product)
    {
        $variants = $product->variants->map(function ($variant) {
            return $variant->options->map(function ($option) {
                return $option->attribute_value_id;
            });
        });

        return $variants;
    }

    public function storeProductVariant($request, $product)
    {
        $variantData = $request->variant;
        $sellingPrices = $request->price;
        $costs = $request->cost;
        $skus = $request->sku;

        foreach ($variantData as $key => $variantInfo) {
            // check if variant already exists
            $existingVariant = $product->variants->where('sku', $skus[$key])->first();

            if ($existingVariant) {
                continue;
            }

            $variantInfoArray = explode('-', $variantInfo);

            // Insert variant into the variants table
            $variant = Variant::create([
                'product_id' => $product->id,
                'sku' => $skus[$key],
                'price' => $sellingPrices[$key],
                'cost' => $costs[$key],
            ]);

            // Insert variant-specific information into the variant_attribute_values table
            foreach ($variantInfoArray as $attributeValue) {
                $attributeValueModel = AttributeValue::where('name', $attributeValue)->first();

                if ($attributeValueModel) {
                    VariantOption::create([
                        'variant_id' => $variant->id,
                        'attribute_id' => $attributeValueModel->attribute_id,
                        'attribute_value_id' => $attributeValueModel->id,
                    ]);
                }
            }
        }
    }

    public function getProductVariantById($variant_id)
    {
        return Variant::with('options', 'optionValues')->find($variant_id);
    }
    public function getProductVariant($variant_id)
    {
        return $this->getProductVariantById($variant_id);
    }
    public function updateProductVariant($request, $variant)
    {
        $variant->update([
            'price' => $request->selling_price,
            'cost' => $request->cost,
            'sku' => $request->sku,
        ]);
        return $variant;
    }

    public function deleteProductVariant($variant)
    {
        // delete variant options
        $variant->options()->delete();

        return $variant->delete();
    }

    /**
     * Bulk import products from Excel/CSV.
     *
     * Expected column layout (0-indexed):
     *  [0] Name
     *  [1] SKU
     *  [2] Category
     *  [3] Barcode
     *  [4] Unit
     *  [5] Brand
     *  [6] Stock Alert
     *  [7] Cost (Purchase Price)
     *  [8] Price (Selling Price)
     *  [9] Stock (Opening Quantity)
     */
    public function bulkImport($request)
    {
        $file = $request->file('file');

        $data = Excel::toCollection(null, $file);
        $data = $data->first()->slice(1); // skip header row

        $unsavedData = [];
        foreach ($data as $row) {
            // Skip empty rows
            if (!isset($row[0]) || empty(trim($row[0] ?? ''))) {
                continue;
            }

            $name       = trim($row[0] ?? '');
            $sku        = trim($row[1] ?? '');
            $catName    = trim($row[2] ?? '');
            $barcode    = trim($row[3] ?? '');
            $unitName   = trim($row[4] ?? '');
            $brandName  = trim($row[5] ?? '');
            $stockAlert = (int) ($row[6] ?? 0);
            $cost       = max(0, (float) ($row[7] ?? 0));
            $price      = max(0, (float) ($row[8] ?? 0));
            $stock      = max(0, (int) ($row[9] ?? 0));

            // Skip duplicate barcode (if barcode is provided)
            if ($barcode) {
                $findProduct = $this->product->where('barcode', $barcode)->first();
                if ($findProduct) {
                    $unsavedData[] = $row;
                    continue;
                }
            }

            // Also skip duplicate SKU
            if ($sku) {
                $findProduct = $this->product->where('sku', $sku)->first();
                if ($findProduct) {
                    $unsavedData[] = $row;
                    continue;
                }
            }

            // Find or create category
            $category = null;
            if ($catName) {
                $category = Category::where('name', $catName)->first();
                if (!$category) {
                    $category = Category::create(['name' => $catName, 'status' => 1]);
                }
            }

            // Find or create brand
            $brand_id = null;
            if ($brandName) {
                $brand = ProductBrand::where('name', $brandName)->first();
                if (!$brand) {
                    $brand = ProductBrand::create(['name' => $brandName, 'status' => '1']);
                }
                $brand_id = $brand->id;
            }

            // Find or create unit
            $unit = null;
            if ($unitName) {
                $unit = UnitType::where('name', $unitName)->first();
                if (!$unit) {
                    $unit = UnitType::create([
                        'name' => $unitName,
                        'ShortName' => $unitName,
                        'status' => 1,
                    ]);
                }
            }

            // Generate SKU if not provided
            if (!$sku) {
                $sku = (string) mt_rand(10000000, 99999999);
            }

            $product = $this->product->create([
                'name'             => $name,
                'sku'              => $sku,
                'category_id'      => $category ? $category->id : null,
                'unit_id'          => $unit ? $unit->id : null,
                'unit_sale_id'     => $unit ? $unit->id : null,
                'unit_purchase_id' => $unit ? $unit->id : null,
                'brand_id'         => $brand_id,
                'stock_alert'      => $stockAlert,
                'barcode'          => $barcode ?: null,
                'cost'             => $cost,
                'price'            => $price,
                'stock'            => $stock,
                'status'           => 1,
            ]);

            // Store opening stock record with proper financial data
            if ($stock > 0) {
                Stock::create([
                    'product_id'     => $product->id,
                    'date'           => now(),
                    'type'           => 'Opening Stock',
                    'in_quantity'    => $stock,
                    'sku'            => $product->sku,
                    'purchase_price' => $cost,
                    'rate'           => $cost,
                    'sale_price'     => $price,
                    'created_by'     => auth('admin')->user()->id,
                ]);
            }
        }

        return $unsavedData;
    }


    public function storeProductGallery($request, $product)
    {
        $images = $request->images;

        $product->images = $images;

        $product->save();
        return $product;
    }
}
