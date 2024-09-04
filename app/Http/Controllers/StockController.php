<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Modules\Product\app\Services\BrandService;
use Modules\Product\app\Services\ProductCategoryService;
use Modules\Product\app\Services\ProductService;

class StockController extends Controller
{
    public function __construct(private ProductService $product, private BrandService $brandService, private ProductCategoryService $categoryService,) {}

    public function index(Request $request)
    {
        $query = $this->product->allActiveProducts($request);

        if (request('keyword')) {
            $query = $query->where(function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%')
                    ->orWhere('sku', 'like', '%' . request()->keyword . '%')
                    ->orWhere('barcode', 'like', '%' . request()->keyword . '%');
            });
        }
        if (request('order_by')) {
            $query = $query->orderBy('id', request('order_by'));
        }
        if (request('brand_id')) {
            $query = $query->where('brand_id', request('brand_id'));
        }
        if (request('category_id')) {
            $query = $query->where('category_id', request('category_id'));
        }
        if (request('stock_status')) {
            $query = $query->where('stock_status', request('stock_status'));
        }
        if ($request->get('par-page')) {
            $products = $query->paginate($request->get('par-page'));
        } else {
            $products = $query->paginate(20);
        }

        $brands = $this->brandService->getActiveBrands();
        $categories = $this->categoryService->getAllProductCategoriesForSelect();
        return view('admin.pages.stock.stock', compact('products', 'brands', 'categories'));
    }
}
