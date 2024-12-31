<?php

namespace Modules\Report\app\Http\Controllers;

use App\Exports\DTSExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Accounts\app\Services\AccountsService;
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
use Modules\Service\app\Models\Service;
use Modules\Supplier\app\Services\SupplierService;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Service\app\Models\ServiceCategory;
use Modules\Supplier\app\Models\SupplierPayment;

class ReportController extends Controller
{

    public function __construct(private BrandService $brandService, private ProductCategoryService $categoryService, private ProductService $productService, private SupplierService $supplierService, private EmployeeService $employeeService, private AccountsService $accountsService)
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

        $sort = request()->order_by ? request()->order_by : 'desc';
        $reports = ProductSale::with('product', 'sale')->where('source', 2)
            ->where(function ($query)  use ($from_date, $to_date, $sort) {
                $query->whereHas('product', function ($q) {
                    $q->with(['brand'])->where('name', 'like', '%' . request()->keyword . '%')

                        ->orWhere('sku', 'like', '%' . request()->keyword . '%')
                        ->orWhere('barcode', 'like', '%' . request()->keyword . '%');
                    if (request('brand_id')) {
                        $q->orWhere('brand_id', request('brand_id'));
                    }
                    if (request('category_id')) {
                        $q->orWhere('category_id', request('category_id'));
                    }
                })
                    ->whereHas('sale', function ($q)  use ($from_date, $to_date, $sort) {
                        $q->where('order_date', '>=', $from_date)
                            ->where('order_date', '<=', $to_date)
                            ->orderBy('order_date', $sort)
                        ;
                    });
            });


        $data['quantity'] = 0;
        $data['sale_return'] = 0;
        $data['purchase_price'] = 0;
        $data['sale_price'] = 0;
        $data['total'] = 0;

        foreach ($reports->get() as $key => $report) {
            $data['quantity'] += $report->quantity;
            $data['sale_return'] += $report->sale_return;
            $data['purchase_price'] += $report->purchase_price;
            $data['sale_price'] += $report->selling_price;
            $data['total'] += $report->sub_total - $report->purchase_price * $report->quantity;
        }


        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $reports = $reports->get();
        } else {
            $reports = $reports->paginate($parpage);
            $reports->appends(request()->query());
        }

        return view('report::other-income', compact('reports', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function dts()
    {
        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();


        $openingBalance = $this->accountsService->getOpeningBalance($fromDate);
        $currentBalance = $this->accountsService->accountBalance($fromDate, $toDate) + $openingBalance;

        $data = collect([]);
        $date = date('Y-m-d');

        // services calculation
        $services = ProductSale::whereNotNull('service_id')->where(function ($query)  use ($fromDate, $toDate) {
            $query->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                $q->where('order_date', '>=', $fromDate);
                $q->where('order_date', '<=', $toDate);
            });
        })->get();

        $serviceCategory = ServiceCategory::where('name', 'Wash')->first()?->id;
        $washId = Service::where('category_id', $serviceCategory)->get()->pluck('id')->toArray();
        $wash = $services->whereIn('service_id', $washId);

        if ($wash?->first()) {
            $washData = [];
            $washData['date'] = now()->parse($wash->first()->sale->order_date)->format('d-M');
            $washData['mode'] = 'Cash';
            $washData['category'] = "Wash";
            $washData['particular'] = '';
            $washData['debit'] = 0;
            $washData['credit'] = (int)$wash->sum('sub_total');
            $washData['iv'] = 0;

            $washData = (object)$washData;
            $data->push($washData);
        }

        $otherServices = $services->whereNotIn('service_id', $washId);
        if ($otherServices?->first()) {
            $serviceData = [];
            $serviceData['date'] = now()->parse($otherServices?->first()->sale->order_date)->format('d-M');
            $serviceData['mode'] = 'Cash';
            $serviceData['category'] = "Service";
            $serviceData['particular'] = '';
            $serviceData['debit'] = 0;
            $serviceData['credit'] = (int)$otherServices?->sum('sub_total');
            $serviceData['iv'] = 0;

            $serviceData = (object)$serviceData;
            $data->push($serviceData);
        }

        $sales = ProductSale::where('source', 1)
            ->where(function ($query)  use ($fromDate, $toDate) {
                $query->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                    $q->where('order_date', '>=', $fromDate);
                    $q->where('order_date', '<=', $toDate);
                });
            })->whereNotNull('product_id')->get();

        // last purchase price
        $lastPurchasePrice = 0;
        foreach ($sales as $sale) {

            $lastPurchasePrice += (int)remove_comma($sale->product->LastPurchasePrice ? $sale->product->LastPurchasePrice : $sale->product->cost) * abs($sale->quantity);
        }


        if ($sales->first()) {
            $newData = [];
            $newData['date'] = now()->parse($sales->first()->sale->order_date)->format('d-M');
            $newData['mode'] = 'Cash';
            $newData['category'] = "Sale (Own Inventory)";
            $newData['particular'] = '';
            $newData['debit'] = 0;
            $newData['credit'] = (int)$sales->sum('sub_total');
            $newData['iv'] = (int)$lastPurchasePrice;

            $newData = (object)$newData;
            $data->push($newData);
        }


        $otherIncome = ProductSale::where('source', 2)
            ->where(function ($query)  use ($fromDate, $toDate) {
                $query->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                    $q->where('order_date', '>=', $fromDate);
                    $q->where('order_date', '<=', $toDate);
                });
            })->get();

        $debit = 0;
        $credit = 0;
        foreach ($otherIncome as $income) {
            $debit += $income->purchase_price * $income->quantity;
            $credit += $income->selling_price * $income->quantity;
        }

        if ($otherIncome->first()) {
            $newData = [];
            $newData['date'] = now()->parse($otherIncome->first()->sale->order_date)->format('d-M');
            $newData['mode'] = 'Cash';
            $newData['category'] = "Other Income (Parts-Local Market)";
            $newData['particular'] = '';
            $newData['debit'] = (int)$debit;
            $newData['credit'] = (int)$credit;
            $newData['iv'] = 0;
            $newData = (object)$newData;
            $data->push($newData);
        }


        // customer dues
        $todaySales = Sale::where(function ($q)  use ($fromDate, $toDate) {
            $q->whereHas('customer_due');
            $q->where('order_date', '>=', $fromDate);
            $q->where('order_date', '<=', $toDate);
        })->get();

        foreach ($todaySales as $sale) {
            $SaleDue = $sale->customer_due->due_amount + $sale->customer_due->paid_amount;
            $newData = [];
            $newData['date'] = now()->parse($sale->order_date)->format('d-M');
            $newData['mode'] = 'Debit';
            $newData['category'] = "Customer Due";
            $newData['particular'] = $sale->customer_due->customer->name;
            $newData['debit'] = (int)$SaleDue;
            $newData['credit'] = 0;
            $newData['iv'] = 0;
            $newData = (object)$newData;
            $data->push($newData);
        }

        // customer due receive

        $customerPayments = CustomerPayment::whereDate('payment_date', '>=', $fromDate)->whereDate('payment_date', '<=', $toDate)->where('payment_type', 'due_receive')->get();

        foreach ($customerPayments as $cusPayment) {
            if ($cusPayment->amount == 0) continue;
            $newData = [];
            $newData['date'] = now()->parse($cusPayment->payment_date)->format('d-M');
            $newData['mode'] = accountList()[$cusPayment->account->account_type];
            $newData['category'] = "Customer Due Receive";
            $newData['particular'] = $cusPayment->customer->name;
            $newData['debit'] = 0;
            $newData['credit'] = (int)$cusPayment->amount;
            $newData['iv'] = 0;
            $newData = (object)$newData;
            $data->push($newData);
        }


        $purchases = Purchase::query();

        if ($fromDate) {
            $purchases = $purchases->where('purchase_date', '>=', $fromDate);
        }

        if ($toDate) {
            $purchases = $purchases->where('purchase_date', '<=', $toDate);
        }
        $purchases = $purchases->with('payments')->get();

        // purchase
        foreach ($purchases as $purchase) {
            if ($purchase->payments->count() > 0 && $purchase->payments->sum('amount')) {
                foreach ($purchase->payments as $payment) {
                    $newData = [];
                    $newData['date'] = now()->parse($purchase->purchase_date)->format('d-M');
                    $newData['mode'] = $payment->account_type;
                    $newData['category'] = "Inventory";
                    $newData['particular'] = $purchase->supplier->name ?? 'Guest';
                    $newData['debit'] = (int)$payment->amount;
                    $newData['credit'] = 0;
                    $newData['iv'] = 0;
                    $newData = (object)$newData;
                    $data->push($newData);
                }
            }

            $due_amount = (int)$purchase->due_amount;
            if ($due_amount) {
                $newData = [];
                $newData['date'] = now()->parse($purchase->purchase_date)->format('d-M');
                $newData['mode'] = 'Credit';
                $newData['category'] = "Inventory";
                $newData['particular'] = $purchase->supplier->name ?? 'Guest';
                $newData['debit'] = (int)$purchase->due_amount;
                $newData['credit'] = 0;
                $newData['iv'] = 0;
                $newData = (object)$newData;
                $data->push($newData);
            }
        }


        // supplier payments
        $supplierPayment = SupplierPayment::whereBetween('payment_date', [$fromDate, $toDate])
            ->whereNotNull('purchase_id')
            ->whereHas('purchase', function ($q) {
                $q->whereColumn(
                    'payment_date',
                    '!=',
                    'purchase_date'
                );
            })
            ->orderBy('purchase_id', 'asc')
            ->get();

        $processedPurchases = [];

        foreach ($supplierPayment as $index => $payment) {
            if (in_array($payment->purchase_id, $processedPurchases)) {
                continue;
            }

            $paymentAmount = $supplierPayment->where('purchase_id', $payment->purchase_id)->sum('amount');
            $count = $supplierPayment->where('purchase_id', $payment->purchase_id)->count();
            if ($payment->purchase->purchase_date != $payment->payment_date) {
                $newData = [];
                $newData['date'] = now()->parse($payment->payment_date)->format('d-M');
                $newData['mode'] = 'R/P Credit';
                $newData['category'] = "Inventory";
                $newData['particular'] = $payment->supplier->name ?? 'Guest';
                $newData['debit'] = (int)$paymentAmount;
                $newData['credit'] = 0;
                $newData['iv'] = 0;
                $newData = (object)$newData;
                $data->push($newData);
                $processedPurchases[] = $payment->purchase_id;
            }
        }


        // expense calculation
        $expenses = Expense::whereBetween('date', [$fromDate, $toDate])->get();

        foreach ($expenses as $expense) {
            $newData = [];
            $newData['date'] = now()->parse($expense->date)->format('d-M');
            $newData['mode'] = 'Cash';
            $newData['category'] = $expense->expenseType->name;
            $newData['particular'] = $expense->note;
            $newData['debit'] = (int)$expense->amount;
            $newData['credit'] = 0;
            $newData['iv'] = 0;

            $newData = (object)$newData;
            $data->push($newData);
        }


        // salary calculation
        $salaries = EmployeeSalary::whereBetween('date', [$fromDate, $toDate])->get();
        foreach ($salaries as $salary) {
            $newData = [];
            $newData['date'] = now()->parse($salary->date)->format('d-M');
            $newData['mode'] = 'Cash';
            $newData['category'] = 'Salary';
            $newData['particular'] = $salary->employee->name;
            $newData['debit'] = (int)$salary->amount;
            $newData['credit'] = 0;
            $newData['iv'] = 0;
            $newData = (object)$newData;
            $data->push($newData);
        }

        if (request('export')) {
            $fileName = 'dts-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
            return Excel::download(new DTSExport($data), $fileName);
        }

        if (request('par-page')) {
            if (request('par-page') == 'all') {
                $perPage = count($data);
            } else {

                $perPage = request('par-page');
            }
        } else {
            $perPage = 20;
        }

        $page = request('page', 1); // Default to page 1
        $paginatedData = $data->slice(($page - 1) * $perPage, $perPage)->values();

        $data = new LengthAwarePaginator(
            $paginatedData,
            count($data),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );


        return view('report::dts', compact('data', 'currentBalance', 'openingBalance'));
    }


    public function barcodeWiseProduct()
    {
        $products = $this->productService->getProducts();
        $products = $products->where('status', 1);

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $products = $products->get();
        } else {
            $products = $products->paginate($parpage);
            $products->appends(request()->query());
        }



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

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $products = $products->get();
        } else {
            $products = $products->paginate($parpage);
            $products->appends(request()->query());
        }

        return view('report::barcode-sale', compact('products', 'totalStock', 'sellCount', 'sellPrice', 'totalPurchasePrice'));
    }


    public function categories()
    {
        $categories = $this->categoryService->getCategories();


        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $categories = $categories->get();
        } else {
            $categories = $categories->paginate($parpage);
            $categories->appends(request()->query());
        }

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
        $customers->appends(request()->query());

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
        $sales->appends(request()->query());

        return view('report::receiveable', compact('sales', 'totalDues'));
    }

    public function detailsSale()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        // ->whereBetween('order_date', [$fromDate, $toDate])
        $sales = Sale::with('customer');
        $sales = $sales->paginate(20);
        $sales->appends(request()->query());
        return view('report::details-sale', compact('sales'));
    }

    public function dueDateSale()
    {
        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        // ->whereBetween('order_date', [$fromDate, $toDate])
        $sales = Sale::with('customer')->where('due_amount', '>', 0);
        $sales = $sales->paginate(20);
        $sales->appends(request()->query());
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
        $expenses->appends(request()->query());
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
        $sales->appends(request()->query());
        return view('report::master-sale', compact('sales', 'totalAmount'));
    }

    public function monthlySale()
    {
        $month = request('month') ? now()->parse(request('month')) : now()->month;

        $sales = Sale::with('customer')->whereMonth('order_date', $month);
        $totalAmount = $sales->sum('grand_total');
        $sales = $sales->paginate(20);
        $sales->appends(request()->query());
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
        $products = $products->appends(request()->query());
        return view('report::product-sale-report', compact('products', 'totalStock', 'sellCount', 'sellPrice', 'totalPurchasePrice'));
    }

    public function  receivedReport()
    {
        $totalReceive = CustomerPayment::where('is_received', 1);

        if (request('from_date') || request('to_date')) {
            $totalReceive = $totalReceive->whereBetween('created_at', [request('from_date'), request('to_date')]);
        }
        $totalReceive = $totalReceive->paginate(20);
        $totalReceive->appends(request()->query());

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
        $purchases->appends(request()->query());
        return view('report::purchase', compact('purchases', 'totalAmount'));
    }

    public function supplier()
    {

        $suppliers = $this->supplierService->allSupplier();
        $suppliers = $suppliers->paginate(20);
        $suppliers->appends(request()->query());

        return view('report::supplier', compact('suppliers'));
    }

    public function salary()
    {
        $month = request('month') ? now()->parse(request('month')) : now()->month;


        $employees = $this->employeeService->all()->get();


        return view('report::salary', compact('employees'));
    }

    public function supplierPayment()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        $supplierPayments = Purchase::with('supplier');
        if (request('from_date') || request('to_date')) {
            $supplierPayments = $supplierPayments->whereBetween('purchase_date', [$fromDate, $toDate]);
        }
        $totalAmount = $supplierPayments->sum('total_amount');
        $supplierPayments = $supplierPayments->paginate(20);
        $supplierPayments->appends(request()->query());

        return view('report::supplier-payment', compact('supplierPayments', 'totalAmount'));
    }
}
