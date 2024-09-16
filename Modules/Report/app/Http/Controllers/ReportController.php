<?php

namespace Modules\Report\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Employee\app\Models\EmployeeSalary;
use Modules\Expense\app\Models\Expense;
use Modules\Product\app\Services\BrandService;
use Modules\Product\app\Services\ProductCategoryService;
use Modules\Product\app\Services\ProductService;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;

class ReportController extends Controller
{

    public function __construct(private BrandService $brandService, private ProductCategoryService $categoryService, private ProductService $productService)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function otherIncome()
    {
        $from_date = request('from_date') ? now()->parse(request('from_date'))->format('Y-m-d') : date('Y-m-d');
        $to_date = request('to_date') ? now()->parse(request('to_date'))->format('Y-m-d') : date('Y-m-d');
        $categories = $this->categoryService->getAllProductCategoriesForSelect();
        $brands = $this->brandService->getActiveBrands();

        $reports = ProductSale::where('source', 2)
            ->where(function ($query)  use ($from_date, $to_date) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . request()->keyword . '%')

                        ->orWhere('sku', 'like', '%' . request()->keyword . '%')
                        ->orWhere('barcode', 'like', '%' . request()->keyword . '%');
                    if (request('brand_id')) {
                        $q->orWhere('brand_id', request('brand_id'));
                    }
                    if (request('category_id')) {
                        $q->orWhere('category_id', request('category_id'));
                    }
                })
                    ->whereHas('sale', function ($q)  use ($from_date, $to_date) {
                        $q->where('order_date', '>=', $from_date)
                            ->where('order_date', '<=', $to_date);
                    });
            })
            ->paginate(20);

        return view('report::other-income', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function dts()
    {
        $date = date('Y-m-d');
        $expenses = Expense::where('date', $date)->get();
        $salaries = EmployeeSalary::where('date', $date)->get();
        $otherIncome = ProductSale::where('source', 2)
            ->where(function ($query)  use ($date) {
                $query->whereHas('sale', function ($q) use ($date) {
                    $q->where('order_date', $date);
                });
            })->sum('sub_total');
        return view('report::dts', compact('expenses', 'salaries', 'otherIncome'));
    }


    public function barcodeWiseProduct()
    {
        $products = $this->productService->getProducts();
        $products = $products->where('status', 1);

        $products = $products->paginate(20);
        return view('report::barcode-wise-product', compact('products'));
    }
}
