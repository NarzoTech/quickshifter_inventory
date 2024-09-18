<?php

namespace Modules\Report\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Employee\app\Models\EmployeeSalary;
use Modules\Expense\app\Models\Expense;
use Modules\Product\app\Services\BrandService;
use Modules\Product\app\Services\ProductCategoryService;
use Modules\Product\app\Services\ProductService;
use Modules\Purchase\app\Models\Purchase;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\SalesReturn;

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

    public function barcodeSale()
    {
        $products = $this->productService->getProducts();
        $products = $products->where('status', 1);
        $totalProducts = $products->get();

        $totalStock = 0;
        $sellCount = 0;
        $sellPrice = 0;
        $totalPurchasePrice = 0;

        $totalProducts->map(function ($product) use (&$totalStock, &$sellCount, &$sellPrice, &$totalPurchasePrice) {
            $sellQty = $product->sales['qty'] - $product->sales_return['qty'];
            $sellCount += $sellQty;

            $sellPrice += $sellQty > 0 ? $product->sales['price'] / $sellQty : 0;

            $totalPurchasePrice += $sellCount * $product->purchase_price;

            $totalStock += $product->stock_count;

            return;
        });

        $products = $products->paginate(20);


        return view('report::barcode-sale', compact('products', 'totalStock', 'sellCount', 'sellPrice', 'totalPurchasePrice'));
    }


    public function categories()
    {
        $categories = $this->categoryService->getCategories();


        $categories = $categories->paginate(20);

        return view('report::categories', compact('categories'));
    }
    public function customers(Request $request)
    {
        $query = User::query();

        $query = $query->with('sales');

        $query->when($request->filled('keyword'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->keyword . '%')
                ->orWhere('email', 'like', '%' . $request->keyword . '%')
                ->orWhere('phone', 'like', '%' . $request->keyword . '%')
                ->orWhere('address', 'like', '%' . $request->keyword . '%');
        });

        $allCustomers = $query->get();

        $totalSales = 0;
        $totalAmount = 0;
        $totalPaid = 0;
        $totalDue = 0;
        foreach ($allCustomers as $customer) {
            $totalSales += $customer->sales->count();
            $totalAmount += $customer->sales->sum('grand_total');
            $totalPaid += $customer->total_paid;
            $totalDue += $customer->total_due;
        }

        $customers = $query->paginate(20);

        return view('report::customer', compact('customers', 'totalSales', 'totalAmount', 'totalPaid', 'totalDue'));
    }

    public function receivable()
    {
        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();

        $sales = Sale::with('customer')->where('payment_status', 1)->where('due_amount', '>', 0);

        $sales = $sales->whereBetween('order_date', [$fromDate, $toDate]);

        $totalDues = $sales->sum('due_amount');
        $sales = $sales->paginate(20);

        return view('report::receiveable', compact('sales', 'totalDues'));
    }

    public function detailsSale()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        // ->whereBetween('order_date', [$fromDate, $toDate])
        $sales = Sale::with('customer');
        $sales = $sales->paginate(20);
        return view('report::details-sale', compact('sales'));
    }

    public function dueDateSale()
    {
        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        // ->whereBetween('order_date', [$fromDate, $toDate])
        $sales = Sale::with('customer')->where('due_amount', '>', 0);
        $sales = $sales->paginate(20);
        return view('report::due-date-sale', compact('sales'));
    }

    public function expense()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        // ->whereBetween('order_date', [$fromDate, $toDate])
        $expenses = Expense::with('createdBy', 'expenseType');
        if (request('from_date') || request('to_date')) {
            $expenses = $expenses->whereBetween('date', [$fromDate, $toDate]);
        }
        $totalAmount = $expenses->sum('amount');
        $expenses = $expenses->paginate(20);
        return view('report::expense', compact('expenses', 'totalAmount'));
    }

    public function masterSale()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        // ->whereBetween('order_date', [$fromDate, $toDate])
        $sales = Sale::with('customer');
        if (request('from_date') || request('to_date')) {
            $sales = $sales->whereBetween('order_date', [$fromDate, $toDate]);
        }

        $totalAmount = $sales->sum('grand_total');
        $sales = $sales->paginate(20);
        return view('report::master-sale', compact('sales', 'totalAmount'));
    }

    public function monthlySale()
    {
        $month = request('month') ? now()->parse(request('month')) : now()->month;

        $sales = Sale::with('customer')->whereMonth('order_date', $month);
        $totalAmount = $sales->sum('grand_total');
        $sales = $sales->paginate(20);
        return view('report::monthly-sale', compact('sales', 'totalAmount'));
    }

    public function profitLoss()
    {
        $data['totalPurchases'] = Purchase::sum('total_amount');
        $data['expenses'] = Expense::sum('amount');
        $data['totalSales'] = Sale::sum('grand_total');
        $data['salesReturns'] = SalesReturn::sum('return_amount');
        $data['totalReceive'] = CustomerPayment::where('is_received', 1)->sum('amount');

        return view('report::profit-loss', compact('data'));
    }

    public function productSaleReport()
    {
        $products = $this->productService->getProducts();
        $products = $products->where('status', 1);
        $totalProducts = $products->get();

        $totalStock = 0;
        $sellCount = 0;
        $sellPrice = 0;
        $totalPurchasePrice = 0;

        $totalProducts->map(function ($product) use (&$totalStock, &$sellCount, &$sellPrice, &$totalPurchasePrice) {
            $sellQty = $product->sales['qty'] - $product->sales_return['qty'];
            $sellCount += $sellQty;

            $sellPrice += $sellQty > 0 ? $product->sales['price'] / $sellQty : 0;

            $totalPurchasePrice += $sellCount * $product->purchase_price;

            $totalStock += $product->stock_count;

            return;
        });

        $products = $products->paginate(20);
        return view('report::product-sale-report', compact('products', 'totalStock', 'sellCount', 'sellPrice', 'totalPurchasePrice'));
    }
}
