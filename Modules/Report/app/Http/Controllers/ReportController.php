<?php

namespace Modules\Report\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Employee\app\Models\EmployeeSalary;
use Modules\Employee\app\Services\EmployeeService;
use Modules\Expense\app\Models\Expense;
use Modules\Product\app\Services\BrandService;
use Modules\Product\app\Services\ProductCategoryService;
use Modules\Product\app\Services\ProductService;
use Modules\Purchase\app\Models\Purchase;
use Modules\Purchase\app\Models\PurchaseDetails;
use Modules\Purchase\app\Models\PurchaseReturn;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\SalesReturn;
use Modules\Service\app\Models\ServiceCategory;
use Modules\Supplier\app\Services\SupplierService;

class ReportController extends Controller
{

    public function __construct(private BrandService $brandService, private ProductCategoryService $categoryService, private ProductService $productService, private SupplierService $supplierService, private EmployeeService $employeeService)
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
        $data = collect([]);
        $date = date('Y-m-d');

        // expense calculation
        $expenses = Expense::where('date', $date)->get();

        foreach ($expenses as $expense) {
            $newData = collect([]);
            $newData['date'] = $expense->date;
            $newData['mode'] = 'Cash';
            $newData['category'] = 'Expense';
            $newData['particular'] = $expense->expenseType->name;
            $newData['debit'] = $expense->amount;
            $newData['credit'] = 0;
            $newData['iv'] = 0;
            $data->push($newData);
        }

        // salary calculation
        $salaries = EmployeeSalary::where('date', $date)->get();
        foreach ($salaries as $salary) {
            $newData = collect([]);
            $newData['date'] = $salary->date;
            $newData['mode'] = 'Cash';
            $newData['category'] = 'Salary';
            $newData['particular'] = $salary->employee->name;
            $newData['debit'] = $salary->amount;
            $newData['credit'] = 0;
            $newData['iv'] = 0;
            $data->push($newData);
        }


        $otherIncome = ProductSale::where('source', 2)
            ->where(function ($query)  use ($date) {
                $query->whereHas('sale', function ($q) use ($date) {
                    $q->where('order_date', $date);
                });
            })->get();


        $newData = collect([]);
        $newData['date'] = $otherIncome->first()->sale->order_date;
        $newData['mode'] = 'Cash';
        $newData['category'] = "Other Income (Parts-Local Market)";
        $newData['particular'] = '';
        $newData['debit'] = $otherIncome->sum('purchase_price');
        $newData['credit'] = $otherIncome->sum('selling_price');
        $newData['iv'] = 0;
        $data->push($newData);


        // services calculation
        $services = ProductSale::whereNotNull('service_id')->where(function ($query)  use ($date) {
            $query->whereHas('sale', function ($q) use ($date) {
                $q->where('order_date', $date);
            });
        })->get();

        $washId = ServiceCategory::where('name', 'Wash')->first()->id;
        $washData = collect([]);
        $washData['date'] = $services->where('service_id', $washId)->first()->sale->order_date;
        $washData['mode'] = 'Cash';
        $washData['category'] = "Wash";
        $washData['particular'] = '';
        $washData['debit'] = 0;
        $washData['credit'] = $otherIncome->sum('price');
        $washData['iv'] = 0;
        $data->push($washData);


        $serviceData = collect([]);
        $serviceData['date'] = $services->where('service_id', '!=', $washId)->first()->sale->order_date;
        $serviceData['mode'] = 'Cash';
        $serviceData['category'] = "Service";
        $serviceData['particular'] = '';
        $serviceData['debit'] = 0;
        $serviceData['credit'] = $services->where('service_id', '!=', $washId)->sum('price');
        $serviceData['iv'] = 0;
        $data->push($serviceData);



        $sales = ProductSale::where('source', 1)
            ->where(function ($query)  use ($date) {
                $query->whereHas('sale', function ($q) use ($date) {
                    $q->where('order_date', $date);
                });
            })->whereNotNull('product_id')->get();

        // last purchase price
        $lastPurchasePrice = 0;
        foreach ($sales as $sale) {
            $lastPurchasePrice += remove_comma($sale->product->LastPurchasePrice) * $sale->quantity;
        }


        $newData = collect([]);
        $newData['date'] = $sales->first()->sale->order_date;
        $newData['mode'] = 'Cash';
        $newData['category'] = "Sale (Own Inventory)";
        $newData['particular'] = '';
        $newData['debit'] = 0;
        $newData['credit'] = $sales->sum('price');
        $newData['iv'] = $lastPurchasePrice;
        $data->push($newData);



        // purchase
        $purchase = Purchase::where('purchase_date', '=', $date)->get();

        $newData = collect([]);
        $newData['date'] = $purchase->first()->purchase_date;
        $newData['mode'] = 'Credit';
        $newData['category'] = "Inventory";
        $newData['particular'] = '';
        $newData['debit'] = $purchase->sum('total_amount');
        $newData['credit'] = 0;
        $newData['iv'] = 0;
        $data->push($newData);


        // purchase return
        $purchaseReturn = PurchaseReturn::where('return_date', '=', $date)->get();
        $newData = collect([]);
        $newData['date'] = $purchaseReturn->first()->return_date;
        $newData['mode'] = 'Credit';
        $newData['category'] = "Purchase Return";
        $newData['particular'] = '';
        $newData['debit'] = 0;
        $newData['credit'] = $purchaseReturn->sum('total_amount');
        $newData['iv'] = 0;
        $data->push($newData);


        // customer due pay

        $newData = collect([]);


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

    public function  receivedReport()
    {
        $totalReceive = CustomerPayment::where('is_received', 1);

        if (request('from_date') || request('to_date')) {
            $totalReceive = $totalReceive->whereBetween('created_at', [request('from_date'), request('to_date')]);
        }
        $totalReceive = $totalReceive->paginate(20);

        return view('report::received-report', compact('totalReceive'));
    }
    public function purchase()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        // ->whereBetween('order_date', [$fromDate, $toDate])
        $purchases = Purchase::with('supplier');
        if (request('from_date') || request('to_date')) {
            $purchases = $purchases->whereBetween('date', [$fromDate, $toDate]);
        }
        $totalAmount = $purchases->sum('total_amount');
        $purchases = $purchases->paginate(20);
        return view('report::purchase', compact('purchases', 'totalAmount'));
    }

    public function supplier()
    {

        $suppliers = $this->supplierService->allSupplier();
        $suppliers = $suppliers->paginate(20);
        return view('report::supplier', compact('suppliers'));
    }

    public function salary()
    {
        $month = request('month') ? now()->parse(request('month')) : now()->month;


        $employees = $this->employeeService->all()->get();


        return view('report::salary', compact('employees'));
    }
}
