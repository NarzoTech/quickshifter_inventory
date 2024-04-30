<?php

namespace Modules\Product\app\Services;


use Modules\Language\app\Enums\TranslationModels;
use Modules\Language\app\Traits\GenerateTranslationTrait;
use Modules\Product\app\Models\ProductBrand;

class BrandService
{
    use GenerateTranslationTrait;
    protected ProductBrand $brand;

    public function __construct(ProductBrand $brand)
    {
        $this->brand = $brand;
    }

    public function all()
    {
        return $this->brand->all();
    }

    // get product paginate
    public function getPaginateBrands()
    {
        return $this->brand;
    }

    // store product brand

    public function store($request)
    {

        $brand = $this->brand->create($request->all());
        $this->generateTranslations(
            TranslationModels::ProductBrand,
            $brand,
            'product_brand_id',
            $request,
        );
        return $brand;

    }

    public function find($id)
    {
        return $this->brand->find($id);
    }

    public function update($request, $id)
    {
        $brand = $this->brand->find($id);
        $brand->update($request->all());

        $this->updateTranslations(
            $brand,
            $request,
            $request->all(),
        );
        return $brand;
    }

    public function delete($id)
    {
        $brand = $this->brand->find($id);

        // delete translations
        $brand->translations()->delete();
        return $brand->delete();
    }

    public function getActiveBrands()
    {
        return $this->brand->where('status', '1')->get();
    }

    public function findBySlug($slug)
    {
        return $this->brand->where('slug', $slug)->first();
    }

    public function getProductByBrand($slug)
    {
        $brand = $this->brand->where('slug', $slug)->first();
        if ($brand) {
            return $brand->products;
        }
        return [];
    }

}
