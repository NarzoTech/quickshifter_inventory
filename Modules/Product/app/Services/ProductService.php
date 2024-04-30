<?php

namespace Modules\Product\app\Services;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\app\Models\Product;
use Modules\Product\app\Models\Variant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Modules\Product\app\Models\VariantOption;
use Modules\Product\app\Models\AttributeValue;
use Modules\Language\app\Enums\TranslationModels;
use Modules\Language\app\Traits\GenerateTranslationTrait;

class ProductService
{
    use GenerateTranslationTrait;
    protected Product $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }
    public function getProducts(): Product
    {
        return $this->product;
    }

    // get all active products
    public function allActiveProducts($request)
    {
        $products = $this->product->where('status', 1)->with('categories');
        if ($request->has('category')) {
            $products = $products->whereHas('categories', function ($query) use ($request) {
                $query->where('slug', $request->category);
            });
        }
        if ($request->has('brand')) {
            $products = $products->whereHas('brand', function ($query) use ($request) {
                $query->where('slug', $request->brand);
            });
        }
        if ($request->has('min_price')) {
            $products = $products->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $products = $products->where('price', '<=', $request->max_price);
        }

        if ($request->has('search')) {
            $products = $products->whereHas('translation', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('sort')) {
            $products = $products->orderBy('price', $request->sort);
        }

        return $products;
    }

    public function getProduct($id): ?Product
    {
        return $this->product->where('id', $id)->first();
    }
    public function storeProduct($request)
    {
        // store product
        $product = $this->product->create([
            'brand_id' => $request->brand_id,
            'user_id' => auth('admin')->user()->id,
            'unit_id' => $request->unit_id,
            'slug' => $request->slug,
            'badge' => $request->badge,
            'image' => $request->image,
            'price' => $request->price,
            'image' => $request->image,
            'discount' => $request->discount,
            'discount_type' => $request->discount_type,
            'min_delivery_time' => $request->min_delivery_time,
            'max_delivery_time' => $request->max_delivery_time,
            'cost_per_item' => $request->cost_per_item,
            'qty' => $request->quantity,
            'video_link' => $request->video_link,
            'stock' => $request->quantity,
            'stock_status' => $request->quantity > 0 ? 'in_stock' : 'out_of_stock',
            'sku' => $request->sku,
            'status' => $request->status,
            'is_featured' => $request->is_featured,
            'is_bestseller' => $request->is_bestseller,
            'is_new' => $request->is_new,
            'is_flash_deal' => $request->is_flash_deal,
            'is_top' => $request->is_top,
            'is_warranty' => $request->is_warranty,
            'warranty_duration' => $request->is_warranty,
            'is_return' => $request->is_returnable,
            'is_pre_order' => $request->is_pre_order,
            'is_partial' => $request->is_partial,
            'partial_amount' => isset($request->partial_amount) ? $request->partial_amount : 0,
            'is_verified' => $request->is_verified,
            'is_cod' => $request->is_cod,
            'release_date' => $request->release_date,
            'max_product' => $request->max_product,
            'show_homepage' => $request->show_homepage,
            'created_by' => auth('admin')->user()->id,
        ]);

        // store product categories
        $product->categories()->sync($request->categories);

        // Generate translations
        $this->generateTranslations(
            TranslationModels::Product,
            $product,
            'product_id',
            $request,
        );
        return $product;
    }

    public function updateProduct($request, $product)
    {
        // update product
        $product->update([
            'brand_id' => $request->brand_id,
            'unit_id' => $request->unit_id,
            'slug' => $request->slug,
            'badge' => $request->badge,
            'image' => $request->image,
            'price' => $request->price,
            'discount' => $request->discount,
            'discount_type' => $request->discount_type,
            'min_delivery_time' => $request->min_delivery_time,
            'max_delivery_time' => $request->max_delivery_time,
            'cost_per_item' => $request->cost_per_item,
            'qty' => $request->quantity,
            'video_link' => $request->video_link,
            'stock' => $request->quantity,
            'stock_status' => $request->quantity > 0 ? 'in_stock' : 'out_of_stock',
            'sku' => $request->sku,
            'status' => $request->status,
            'is_featured' => $request->is_featured,
            'is_bestseller' => $request->is_bestseller,
            'is_new' => $request->is_new,
            'is_flash_deal' => $request->is_flash_deal,
            'is_top' => $request->is_top,
            'is_warranty' => $request->is_warranty,
            'warranty_duration' => $request->is_warranty,
            'is_return' => $request->is_returnable,
            'is_pre_order' => $request->is_pre_order,
            'is_partial' => $request->is_partial,
            'partial_amount' => $request->partial_amount ? $request->partial_amount : 0,
            'is_verified' => $request->is_verified,
            'is_cod' => $request->is_cod,
            'release_date' => $request->release_date,
            'max_product' => $request->max_product,
            'show_homepage' => $request->show_homepage,
        ]);

        // update product categories
        $product->categories()->sync($request->categories);

        // update translations
        $this->updateTranslations(
            $product,
            $request,
            $request->all(),
        );

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
        $sellingPrices = $request->selling_price;
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

    public function bulkImport($request)
    {

        $file = $request->file('file');

        // read xlxs / xls / csv file
        $data = Excel::toCollection(null, $file);

        $data = $data->first()->slice(0);
        //  remove the first row
        $data = $data->slice(1);


        //  loop through the data and store the products
        $unsavedData = [];
        foreach ($data as $row) {

            $findProduct = $this->product->where(function ($q) use ($row) {
                $q->where('slug', trim($row[10]))
                    ->orWhere('sku', trim($row[12]));
            })->first();

            if ($findProduct) {
                $unsavedData[] = $row;
                continue;
            }

            // make tags
            $lists = explode(',', trim($row[11]));
            $lists = array_map('trim', $lists);
            $tags = [];
            foreach ($lists as $tag) {
                $tags[] = ['value' => $tag];
            }

            $product = $this->product->create([
                'slug' => trim($row[10])  != null ? trim($row[10]) :  Str::slug(trim($row[1])),
                'unit_id' => trim($row[2]),
                'brand_id' => trim($row[3]),
                'discount' => trim($row[5]),
                'discount_type' => trim($row[6]),
                'sku' => trim($row[12]),
                'status' => 1, // 'active'
                'price' => trim($row[13]),
                'created_by' => auth('admin')->user()->id,
            ]);
            // store product categories
            $cats = explode(',', trim($row[4]));
            $cats = array_map('trim', $cats);

            $product->categories()->sync($cats);


            // make a request object using the row data
            $request = new Request();
            $request->replace([
                'name' => trim($row[1]),
                'slug' => trim($row[10])  != null ? trim($row[10]) :  Str::slug(trim($row[1])),
                'unit_id' => trim($row[2]),
                'brand_id' => trim($row[3]),
                'discount' => trim($row[5]),
                'discount_type' => trim($row[6]),
                'short_description' => trim($row[7]),
                'description' => trim($row[8]),
                'additional_information' => trim($row[9]),
                'tags' => json_encode($tags),
                'sku' => trim($row[12]),
                'price' => trim($row[13]),
                'meta_title' => trim($row[14]),
                'meta_description' => trim($row[15]),
            ]);




            $this->generateTranslations(
                TranslationModels::Product,
                $product,
                'product_id',
                $request,
            );
        }
    }



    // make an excel file and download it

    public function downloadUnsavedData($unsavedData)
    {
        $fileName = 'unsaved_products_' . time() . '.xlsx';
        $filePath = public_path('excel_files/' . $fileName);

        $data = collect($unsavedData);

        $data->prepend([
            'id',
            'Name',
            'Unit',
            'Brand',
            'Category',
            'Discount',
            'Discount Type',
            'Short Description',
            'Description',
            'Additional Information',
            'Slug',
            'Tags',
            'SKU',
            'Price',
            'Meta Title',
            'Meta Description',
        ]);

        $file = Excel::store(function ($excel) use ($data) {
            $excel->sheet('Sheet1', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        }, $filePath);

        // store the file to $filePath



        return response()->download($filePath);
    }

    public function storeProductGallery($request, $product)
    {
        $images = $request->images;

        $product->images = $images;

        $product->save();
        return $product;
    }
}
