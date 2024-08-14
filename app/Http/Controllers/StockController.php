<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Modules\Product\app\Services\ProductService;

class StockController extends Controller
{
    public function __construct(private ProductService $product) {}

    public function index(Request $request)
    {
        $products = $this->product->allActiveProducts($request);

        if ($request->get('par-page')) {
            $products = $products->paginate($request->get('par-page'));
        } else {
            $products = $products->paginate(20);
        }
        return view('admin.pages.stock.stock', compact('products'));
    }
}
