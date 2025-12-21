<?php

namespace Modules\Report\app\Http\Controllers;

use App\Exports\BarcodeWiseProductExport;
use App\Exports\BarcodeWiseSaleExport;
use App\Exports\CategoryWiseExport;
use App\Exports\CustomerReportExport;
use App\Exports\DetailsSaleReportExport;
use App\Exports\DTSExport;
use App\Exports\DueDateSaleReportExport;
use App\Exports\ExpenseReportExport;
use App\Exports\OtherIncomeExport;
use App\Exports\PurchaseReportExport;
use App\Exports\ReceivableReportExport;
use App\Exports\SalaryReportExport;
use App\Exports\SuppliersPaymentReportExport;
use App\Exports\SuppliersReportExport;
use App\Exports\TotalReceiveReportExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
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
        checkAdminHasPermissionAndThrowException('other.income.view');
        $categories = $this->categoryService->getAllProductCategoriesForSelect();
        $brands = $this->brandService->getActiveBrands();

        $sort = request()->order_by ? request()->order_by : 'desc';
        $reports = ProductSale::with('product', 'sale')->where('source', 2)
            ->where(function ($query)  use ($sort) {
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
                    ->whereHas('sale', function ($q)  use ($sort) {
                        // Only filter by date if dates are provided
                        if (request('from_date') || request('to_date')) {
                            $from_date = request('from_date') ? now()->parse(request('from_date'))->format('Y-m-d') : now()->subYear()->format('Y-m-d');
                            $to_date = request('to_date') ? now()->parse(request('to_date'))->format('Y-m-d') : date('Y-m-d');
                            $q->where('order_date', '>=', $from_date)
                                ->where('order_date', '<=', $to_date);
                        }
                        $q->orderBy('order_date', $sort);
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

        if (checkAdminHasPermission('other.income.excel.download')) {
            if (request('export')) {
                $fileName = 'other-income-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new OtherIncomeExport($reports), $fileName);
            }
        }
        if (checkAdminHasPermission('other.income.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.other-income', [
                    'reports' => $reports,
                ]);
            }
        }

        return view('report::other-income', compact('reports', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function dts()
    {
        checkAdminHasPermissionAndThrowException('dts.view');
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
                    $newData['mode'] = ucfirst($payment->account->account_type);
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



        if (request('par-page')) {
            if (request('par-page') == 'all') {
                $perPage = count($data);
            } else {

                $perPage = request('par-page');
            }
        } else {
            $perPage = 20;
        }

        if (request('export')) {
            $fileName = 'dts-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
            return Excel::download(new DTSExport($data), $fileName);
        }

        if (checkAdminHasPermission('dts.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.dts', [
                    'data' => $data,
                    'currentBalance' => $currentBalance,
                    'openingBalance' => $openingBalance
                ]);
            }
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

        // Calculate totals from ALL products before pagination
        $allProducts = $products->get();

        $totalSalePrice = 0;
        $totalSaleQty = 0;
        $totalReturnPrice = 0;
        $totalReturnQty = 0;
        $totalPurchasePrice = 0;
        $totalPurchaseQty = 0;

        foreach ($allProducts as $product) {
            $totalSalePrice += (int) $product->sales['price'];
            $totalSaleQty += $product->sales['qty'];
            $totalReturnPrice += (int) $product->sales_return['price'];
            $totalReturnQty += $product->sales_return['qty'];
            $totalPurchasePrice += (int) $product->total_purchase['price'];
            $totalPurchaseQty += $product->total_purchase['qty'];
        }

        $data = [
            'totalSalePrice' => $totalSalePrice,
            'totalSaleQty' => $totalSaleQty,
            'totalReturnPrice' => $totalReturnPrice,
            'totalReturnQty' => $totalReturnQty,
            'totalPurchasePrice' => $totalPurchasePrice,
            'totalPurchaseQty' => $totalPurchaseQty,
        ];

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $products = $allProducts;
        } else {
            $products = $this->productService->getProducts()->where('status', 1)->paginate($parpage);
            $products->appends(request()->query());
        }

        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'barcode-wise-product-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new BarcodeWiseProductExport($allProducts, $data), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.barcode-wise-product', [
                    'products' => $allProducts,
                    'data' => $data
                ]);
            }
        }


        return view('report::barcode-wise-product', compact('products', 'data'));
    }

    public function barcodeSale()
    {
        $products = $this->productService->getProducts();
        $products = $products->where('status', 1);
        $allProducts = $products->get();

        $totalStock = 0;
        $sellCount = 0;
        $sellPrice = 0;
        $totalPurchasePrice = 0;
        $totalProfitLoss = 0;

        foreach ($allProducts as $product) {
            $sellQty = $product->sales['qty'] - $product->sales_return['qty'];
            $sellingPrice = $sellQty > 0 ? $product->sales['price'] / $sellQty : 0;
            $profitLoss = $sellQty * $sellingPrice - $sellQty * $product->purchase_price;

            $totalStock += $product->stock_count;
            $sellCount += $sellQty;
            $sellPrice += $sellingPrice;
            $totalPurchasePrice += $product->purchase_price;
            $totalProfitLoss += $profitLoss;
        }

        $data = [
            'totalStock' => $totalStock,
            'sellCount' => $sellCount,
            'sellPrice' => $sellPrice,
            'totalPurchasePrice' => $totalPurchasePrice,
            'totalProfitLoss' => $totalProfitLoss,
        ];

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $products = $allProducts;
        } else {
            $products = $this->productService->getProducts()->where('status', 1)->paginate($parpage);
            $products->appends(request()->query());
        }

        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'barcode-wise-sale-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new BarcodeWiseSaleExport($allProducts, $data), $fileName);
            }
        }
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.barcode-sale', [
                    'products' => $allProducts,
                    'data' => $data
                ]);
            }
        }

        return view('report::barcode-sale', compact('products', 'totalStock', 'sellCount', 'sellPrice', 'totalPurchasePrice'));
    }


    public function categories()
    {
        $categories = $this->categoryService->getCategories();

        // Calculate totals from ALL categories before pagination
        $allCategories = $categories->get();

        $data = [
            'totalPurchaseCount' => 0,
            'totalSalesCount' => 0,
            'totalPurchaseAmount' => 0,
            'totalSalesAmount' => 0,
        ];

        foreach ($allCategories as $category) {
            $data['totalPurchaseCount'] += $category->PurchaseSummary['count'];
            $data['totalSalesCount'] += $category->sales_count;
            $data['totalPurchaseAmount'] += $category->PurchaseSummary['amount'];
            $data['totalSalesAmount'] += $category->sales_amount;
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $categories = $allCategories;
        } else {
            $categories = $this->categoryService->getCategories()->paginate($parpage);
            $categories->appends(request()->query());
        }

        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'category-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new CategoryWiseExport($allCategories, $data), $fileName);
            }
        }
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.categories', [
                    'categories' => $allCategories,
                    'data' => $data
                ]);
            }
        }

        return view('report::categories', compact('categories', 'data'));
    }
    public function customers(Request $request)
    {
        $query = User::query();

        $query = $query->with(['sales', 'payment']);

        $query->when($request->filled('keyword'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->keyword . '%')
                ->orWhere('email', 'like', '%' . $request->keyword . '%')
                ->orWhere('phone', 'like', '%' . $request->keyword . '%')
                ->orWhere('address', 'like', '%' . $request->keyword . '%');
        });

        // Order by name ascending
        $query->orderBy('name', 'asc');

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

        $data = [
            'totalSales' => $totalSales,
            'totalAmount' => $totalAmount,
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue,
        ];

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $customers = $allCustomers;
        } else {
            $customers = User::with(['sales', 'payment'])->orderBy('name', 'asc')->paginate($parpage);
            $customers->appends(request()->query());
        }

        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'customers-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new CustomerReportExport($allCustomers, $data), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.customer-report', [
                    'customers' => $allCustomers,
                    'data' => $data
                ]);
            }
        }

        return view('report::customer', compact('customers', 'totalSales', 'totalAmount', 'totalPaid', 'totalDue'));
    }

    public function receivable()
    {
        $sales = Sale::with('customer')->where('payment_status', 1)->where('due_amount', '>', 0);

        if (request()->keyword) {
            $sales = $sales->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%');
            })
                ->orWhere('invoice', request()->keyword);
        }

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $sales = $sales->whereBetween('order_date', [$fromDate, $toDate]);
        }

        $totalDues = $sales->sum('due_amount');

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $sales = $sales->get();
        } else {
            $sales = $sales->paginate($parpage);
            $sales->appends(request()->query());
        }

        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'receivable-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new ReceivableReportExport($sales), $fileName);
            }
        }
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.receivable', [
                    'sales' => $sales
                ]);
            }
        }

        return view('report::receiveable', compact('sales', 'totalDues'));
    }

    public function detailsSale()
    {
        $sales = Sale::with(['customer', 'payment', 'payment.account', 'saleReturns']);

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $sales = $sales->whereBetween('order_date', [$fromDate, $toDate]);
        }

        if (request()->keyword) {
            $sales = $sales->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%');
            })
                ->orWhere('invoice', request()->keyword);
        }


        $data['sale_amount'] = 0;
        $data['total_amount'] = 0;
        $data['paid_amount'] = 0;
        $data['due_amount'] = 0;
        $data['return_amount'] = 0;
        foreach ($sales->get() as $sale) {
            $data['sale_amount'] += $sale->total_price;
            $data['total_amount'] += $sale->grand_total;
            $data['paid_amount'] += $sale->paid_amount;
            $data['due_amount'] += $sale->due_amount;
            $data['return_amount'] += $sale->saleReturns->sum('return_amount');
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $sales = $sales->get();
        } else {
            $sales = $sales->paginate($parpage);
            $sales->appends(request()->query());
        }


        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'details-sale-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new DetailsSaleReportExport($sales), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.details-sale', [
                    'sales' => $sales
                ]);
            }
        }
        return view('report::details-sale', compact('sales', 'data'));
    }

    public function dueDateSale()
    {
        $sales = Sale::with(['customer', 'payment', 'payment.account', 'saleReturns'])->where('due_amount', '>', 0);

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $sales = $sales->whereBetween('order_date', [$fromDate, $toDate]);
        }

        if (request()->keyword) {
            $sales = $sales->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%');
            })
                ->orWhere('invoice', request()->keyword);
        }

        $data['due'] = 0;
        foreach ($sales->get() as $sale) {
            $data['due'] += $sale->due_amount;
        }


        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $sales = $sales->get();
        } else {
            $sales = $sales->paginate($parpage);
            $sales->appends(request()->query());
        }


        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'due-date-sale-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new DueDateSaleReportExport($sales), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.due-date-sale', [
                    'sales' => $sales
                ]);
            }
        }
        return view('report::due-date-sale', compact('sales', 'data'));
    }

    public function expense()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        $expenses = Expense::with('createdBy', 'expenseType');

        if (request()->keyword) {
            $expenses = $expenses->where(function ($q) {
                $q->where('note', 'like', '%' . request()->keyword . '%')
                    ->orWhereHas('expenseType', function ($query) {
                        $query->where('name', 'like', '%' . request()->keyword . '%');
                    });
            });
        }

        if (request('from_date') || request('to_date')) {
            $expenses = $expenses->whereBetween('date', [$fromDate, $toDate]);
        }
        $totalAmount = $expenses->sum('amount');

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $expenses = $expenses->get();
        } else {
            $expenses = $expenses->paginate($parpage);
            $expenses->appends(request()->query());
        }
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'expense-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new ExpenseReportExport($expenses), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.expense', [
                    'expenses' => $expenses
                ]);
            }
        }
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
        checkAdminHasPermissionAndThrowException('report.view');

        // Date filtering - default to current date
        $fromDate = request('from_date') ? Carbon::parse(request('from_date'))->startOfDay() : now()->startOfDay();
        $toDate = request('to_date') ? Carbon::parse(request('to_date'))->endOfDay() : now()->endOfDay();

        // Income
        $data['totalSales'] = Sale::whereBetween('order_date', [$fromDate, $toDate])->sum('grand_total');
        $data['salesReturns'] = SalesReturn::whereBetween('created_at', [$fromDate, $toDate])->sum('return_amount');
        $data['netSales'] = $data['totalSales'] - $data['salesReturns'];

        // Purchase Returns (income - money received back from supplier)
        $data['purchaseReturns'] = PurchaseReturn::whereBetween('created_at', [$fromDate, $toDate])->sum('return_amount');

        // Total Income
        $data['totalIncome'] = $data['netSales'] + $data['purchaseReturns'];

        // Expenses
        $data['totalPurchases'] = Purchase::whereBetween('purchase_date', [$fromDate, $toDate])->sum('total_amount');
        $data['expenses'] = Expense::whereBetween('date', [$fromDate, $toDate])->sum('amount');
        $data['salaries'] = EmployeeSalary::whereBetween('date', [$fromDate, $toDate])->sum('amount');

        // Total Expenses
        $data['totalExpenses'] = $data['totalPurchases'] + $data['expenses'] + $data['salaries'];

        // Profit/Loss Calculation
        $data['profitLoss'] = $data['totalIncome'] - $data['totalExpenses'];

        // Date range for display
        $data['fromDate'] = $fromDate->format('d-m-Y');
        $data['toDate'] = $toDate->format('d-m-Y');

        // Excel Export
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'profit-loss-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new \App\Exports\ProfitLossExport($data), $fileName);
            }
        }

        // PDF Export
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.profit-loss', compact('data'));
            }
        }

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
        $totalReceive = CustomerPayment::with(['account', 'sale', 'customer'])->where('is_received', 1)->where('amount', '>', 0);

        if (request('from_date') || request('to_date')) {
            $totalReceive = $totalReceive->whereBetween('created_at', [request('from_date'), request('to_date')]);
        }


        if (request()->keyword) {
            $totalReceive = $totalReceive->where(function ($q) {
                $q->whereHas('customer', function ($query) {
                    $query->where('name', 'like', '%' . request()->keyword . '%');
                });
            });
        }

        $data['receive'] = 0;

        foreach ($totalReceive->get() as $receive) {
            $data['receive'] += $receive->amount;
        }


        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $totalReceive = $totalReceive->get();
        } else {
            $totalReceive = $totalReceive->paginate($parpage);
            $totalReceive->appends(request()->query());
        }

        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'received-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new TotalReceiveReportExport($totalReceive), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.received-report', [
                    'totalReceive' => $totalReceive
                ]);
            }
        }

        return view('report::received-report', compact('totalReceive', 'data'));
    }
    public function purchase()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();

        $purchases = Purchase::with(['supplier', 'purchaseDetails', 'createdBy']);
        if (request('from_date') || request('to_date')) {
            $purchases = $purchases->whereBetween('purchase_date', [$fromDate, $toDate]);
        }

        if (request()->keyword) {
            $purchases = $purchases->where(function ($q) {
                $q->whereHas('supplier', function ($query) {
                    $query->where('name', 'like', '%' . request()->keyword . '%');
                })
                    ->orWhere('invoice_number', request()->keyword);
            });
        }

        $totalAmount = $purchases->sum('total_amount');

        $data['total_amount'] = 0;
        $data['paid_amount'] = 0;
        $data['due_amount'] = 0;

        foreach ($purchases->get() as $purchase) {

            $data['total_amount'] += $purchase->total_amount;
            $data['paid_amount'] += $purchase->paid_amount;
            $data['due_amount'] += $purchase->due_amount;
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $purchases = $purchases->get();
        } else {
            $purchases = $purchases->paginate($parpage);
            $purchases->appends(request()->query());
        }


        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'purchase-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new PurchaseReportExport($purchases), $fileName);
            }
        }
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.purchase', [
                    'purchases' => $purchases
                ]);
            }
        }
        return view('report::purchase', compact('purchases', 'data'));
    }

    public function supplier()
    {
        $suppliers = $this->supplierService->allSupplier();


        $data['totalPurchase'] = 0;
        $data['pay'] = 0;
        $data['total_return'] = 0;
        $data['total_return_pay'] = 0;
        $data['total_due'] = 0;
        $data['purchase_count'] = 0;

        $supplierData = request()->order_type ? $suppliers : $suppliers->get();

        foreach ($supplierData as $supplier) {
            $data['totalPurchase'] += $supplier->purchases->sum('total_amount');
            $data['pay'] += $supplier->payments->sum('amount');

            $totalReturn = $supplier->purchaseReturn->sum('return_amount');
            $data['total_return'] += $totalReturn;

            $data['total_return_pay'] += $supplier->purchaseReturn->sum(
                'received_amount',
            );

            $data['total_due'] += $supplier->total_due - $totalReturn;
            $data['purchase_count'] += $supplier->purchases->count();
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $suppliers = $suppliers->get();
        } else {
            $suppliers = $suppliers->paginate($parpage);
            $suppliers->appends(request()->query());
        }


        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'suppliers-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new SuppliersReportExport($suppliers), $fileName);
            }
        }
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.supplier', [
                    'suppliers' => $suppliers
                ]);
            }
        }

        return view('report::supplier', compact('suppliers', 'data'));
    }



    public function supplierPayment()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        $supplierPayments = Purchase::with(['supplier', 'purchaseReturn']);

        if (request()->keyword) {
            $supplierPayments = $supplierPayments->where(function ($q) {
                $q->whereHas('supplier', function ($query) {
                    $query->where('name', 'like', '%' . request()->keyword . '%');
                })
                    ->orWhere('invoice_number', request()->keyword);
            });
        }
        if (request('from_date') || request('to_date')) {
            $supplierPayments = $supplierPayments->whereBetween('purchase_date', [$fromDate, $toDate]);
        }


        $data['total'] = $supplierPayments->sum('total_amount');
        $data['paid_amount'] = 0;
        $data['due_amount'] = 0;
        $data['return_amount'] = 0;

        foreach ($supplierPayments->get() as $payment) {
            $data['paid_amount'] += $payment->paid_amount;
            $data['due_amount'] += $payment->due_amount - $payment->purchaseReturn->sum('return_amount') + $payment->purchaseReturn->sum('received_amount');
            $data['return_amount'] += $payment->purchaseReturn->sum('return_amount');
        }

        $totalAmount = $supplierPayments->sum('total_amount');

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $supplierPayments = $supplierPayments->get();
        } else {
            $supplierPayments = $supplierPayments->paginate($parpage);
            $supplierPayments->appends(request()->query());
        }

        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'suppliers-payment-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new SuppliersPaymentReportExport($supplierPayments), $fileName);
            }
        }
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.supplier-payment', [
                    'supplierPayments' => $supplierPayments
                ]);
            }
        }

        return view('report::supplier-payment', compact('supplierPayments', 'totalAmount', 'data'));
    }

    public function salary()
    {
        $months = [];
        $years = [];
        if (request('from_date') && request('to_date')) {
            $fromDate = Carbon::createFromFormat('d/m/Y', '01/' . request('from_date'));
            $toDate = Carbon::createFromFormat('d/m/Y', '01/' . request('to_date'));
            while ($fromDate <= $toDate) {
                $months[] = $fromDate->format('F');
                $years[] = $fromDate->year;
                $fromDate->addMonth();
            }
        } else {
            for ($month = 1; $month <= 12; $month++) {
                $months[] = Carbon::createFromDate(null, $month)->format('F');
                $years[] = now()->year;
            }
        }

        $employees = $this->employeeService->all();

        if (request()->keyword) {
            $employees = $employees->where('name', 'like', '%' . request()->keyword . '%');
        }
        if (request()->order_by) {
            $employees = $employees->orderBy('name', request()->order_by);
        } else {
            $employees = $employees->orderBy('name');
        }


        $employees = $employees->get()->map(function ($employee) use ($months, $years) {
            $totalSalary = 0;
            $paidSalary = 0;

            foreach ($months as $index => $month) {

                $year = $years[$index];

                $requestData = [
                    'month' => $month,
                    'year' => $year
                ];

                $newRequest = new Request($requestData);


                [,,,
                    $payableSalary
                ] = $this->employeeService->calculateSalary($newRequest, $employee->id);


                $totalSalary += $payableSalary;
                $paidSalary += $employee->currentSalary->where('month', $month)->where('year', $year)->sum('amount');
            }

            $employee->total_salary = $totalSalary;
            $employee->paid_salary = $paidSalary;

            return $employee;
        });


        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'salaries-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new SalaryReportExport($employees), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.salary', [
                    'employees' => $employees
                ]);
            }
        }


        return view('report::salary', compact('employees'));
    }
}
