<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Expense\app\Models\Expense;
use Modules\Language\app\Models\Language;
use Modules\Product\app\Models\Product;
use Modules\Purchase\app\Models\Purchase;
use Modules\Purchase\app\Services\PurchaseService;
use Modules\Sales\app\Models\Sale;
use Modules\Supplier\app\Services\SupplierService;

class DashboardController extends Controller
{

    public function __construct(private SupplierService $supplierService, private PurchaseService $purchaseService)
    {
        $this->middleware('auth:admin');
    }
    public function dashboard()
    {
        $data['customerDues'] = CustomerDue::where('status', 1)->sum('due_amount');
        $data['todaySales'] = Sale::whereDate('order_date', date('Y-m-d'))->sum('grand_total');
        $data['totalProducts'] = Product::count();

        $suppliers = $this->supplierService->allSupplier();

        $data['total_supplier_due'] = 0;
        foreach ($suppliers->get() as $supplier) {
            $totalReturn = $supplier->purchaseReturn->sum('return_amount');
            $data['total_supplier_due'] += $supplier->total_due - $totalReturn;
        }

        $data['suppliersDues'] = 0;



        $purchases = $this->purchaseService->all()
            ->selectRaw('DATE_FORMAT(purchase_date, "%Y-%m") as month, SUM(total_amount) as total')
            ->where('purchase_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $purchasesData = $purchases->mapWithKeys(function ($purchase) {
            return [$purchase->month => $purchase->total];
        });



        $purchaseData = collect(Carbon::today()->startOfMonth()->subMonths(11)->monthsUntil(Carbon::today()->startOfMonth()))
            ->mapWithKeys(fn($date) => [$date->format('Y-m') => 0])
            ->merge($purchasesData)
            ->sortKeys();


        $sales = Sale::selectRaw('DATE_FORMAT(order_date, "%Y-%m") as month, SUM(grand_total) as total')
            ->where('order_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $salesData = $sales->mapWithKeys(function ($sale) {
            return [$sale->month => $sale->total];
        });

        $saleData = collect(Carbon::today()->startOfMonth()->subMonths(11)->monthsUntil(Carbon::today()->startOfMonth()))
            ->mapWithKeys(fn($date) => [$date->format('Y-m') => 0])
            ->merge($salesData)
            ->sortKeys();

        // current month sales with dates
        $currentMonthSales = Sale::selectRaw('DATE_FORMAT(order_date, "%Y-%m-%d") as date, SUM(grand_total) as total')
            ->where('order_date', '>=', now()->startOfMonth())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $currentMonthSalesData = $currentMonthSales->mapWithKeys(function ($sale) {
            return [$sale->date => $sale->total];
        });

        $currentMonthDates = collect(Carbon::now()->startOfMonth()->daysUntil(Carbon::now()->endOfMonth()))
            ->mapWithKeys(fn($date) => [$date->format('Y-m-d') => 0]);
        $chart['currentMonthSaleData'] = $currentMonthDates
            ->merge($currentMonthSalesData)
            ->sortKeys();

        // current month expense
        $chart['currentMonthExpense'] = Expense::where('date', '>=', now()->startOfMonth())->sum('amount');

        // last month expense
        $chart['lastMonthExpense'] = Expense::whereBetween('date', [
            now()->subMonthsNoOverflow()->startOfMonth(),
            now()->subMonthsNoOverflow()->endOfMonth(),
        ])->sum('amount');


        // calculate if current month expense is greater/smaller than last month expense and calculate percentage
        if ($chart['currentMonthExpense'] > $chart['lastMonthExpense']) {
            $chart['expensePercentage'] = ($chart['currentMonthExpense'] - $chart['lastMonthExpense']) / $chart['lastMonthExpense'] * 100;
        } elseif ($chart['currentMonthExpense'] < $chart['lastMonthExpense']) {
            $chart['expensePercentage'] = - ($chart['lastMonthExpense'] - $chart['currentMonthExpense']) / $chart['lastMonthExpense'] * 100;
        } else {
            $chart['expensePercentage'] = 0;
        }
        $chart['expensePercentage'] = number_format($chart['expensePercentage'], 2);



        // current month sales
        $chart['currentSales'] = Sale::where('order_date', '>=', now()->startOfMonth())->sum('grand_total');
        $lastSales = Sale::whereBetween('order_date', [
            now()->subMonthsNoOverflow()->startOfMonth(),
            now()->subMonthsNoOverflow()->endOfMonth(),
        ])->sum('grand_total');


        if ($chart['currentSales'] > $lastSales) {
            $chart['salePercentage'] = ($chart['currentSales'] - $lastSales) / $lastSales * 100;
        } elseif ($chart['currentSales'] < $lastSales) {

            $chart['salePercentage'] = - ($lastSales - $chart['currentSales']) / $lastSales * 100;
        } else {
            $chart['salePercentage'] = 0;
        }
        $chart['salePercentage'] = number_format($chart['salePercentage'], 2);

        // current month purchase
        $chart['currentPurchases'] = Purchase::where('purchase_date', '>=', now()->startOfMonth())->sum('total_amount');
        $lastPurchases = Purchase::whereBetween('purchase_date', [
            now()->subMonthsNoOverflow()->startOfMonth(),
            now()->subMonthsNoOverflow()->endOfMonth(),
        ])->sum('total_amount');

        if ($chart['currentPurchases'] > $lastPurchases) {
            $chart['purchasePercentage'] = ($chart['currentPurchases'] - $lastPurchases) / $lastPurchases * 100;
        } elseif ($chart['currentPurchases'] < $lastPurchases) {
            $chart['purchasePercentage'] = - ($lastPurchases - $chart['currentPurchases']) / $lastPurchases * 100;
        } else {
            $chart['purchasePercentage'] = 0;
        }
        $chart['purchasePercentage'] = number_format($chart['purchasePercentage'], 2);
        return view('admin.dashboard', compact('data', 'purchaseData', 'saleData', 'chart'));
    }

    public function setLanguage()
    {
        $lang = Language::whereCode(request('code'))->first();

        if (session()->has('lang')) {
            session()->forget('lang');
            session()->forget('text_direction');
        }
        if ($lang) {
            session()->put('lang', $lang->code);
            session()->put('text_direction', $lang->direction);

            $notification = __('Language Changed Successfully');
            $notification = ['messege' => $notification, 'alert-type' => 'success'];

            return redirect()->back()->with($notification);
        }

        session()->put('lang', config('app.locale'));

        $notification = __('Language Changed Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }
}
