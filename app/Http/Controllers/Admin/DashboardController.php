<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Language\app\Models\Language;
use Modules\Product\app\Models\Product;
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

        return view('admin.dashboard', compact('data', 'purchaseData', 'saleData'));
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
