<?php

namespace Modules\Report\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Product\app\Services\BrandService;
use Modules\Product\app\Services\ProductCategoryService;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;

class ReportController extends Controller
{

    public function __construct(private BrandService $brandService, private ProductCategoryService $categoryService)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function otherIncome()
    {
        $categories = $this->categoryService->getAllProductCategoriesForSelect();
        $brands = $this->brandService->getActiveBrands();

        $reports = ProductSale::where('source', 2)
            ->where(function ($query) {
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
                });
            })
            ->paginate(20);

        return view('report::other-income', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('report::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('report::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('report::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
