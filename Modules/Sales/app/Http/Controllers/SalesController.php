<?php

namespace Modules\Sales\app\Http\Controllers;

use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\app\Models\Account;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Customer\app\Http\Services\UserGroupService;
use Modules\Customer\app\Models\Vehicle;
use Modules\POS\app\Models\CartHold;
use Modules\Product\app\Models\Category;
use Modules\Product\app\Models\Product;
use Modules\Product\app\Services\BrandService;
use Modules\Sales\app\Services\SaleService;
use Modules\Service\app\Services\ServicesService;

class SalesController extends Controller
{
    public function __construct(private UserGroupService $userGroup, private SaleService $saleService, private BrandService $brandService, private AreaService $areaService, private Vehicle $vehicle, private ServicesService $services)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('sales.view');
        $sales = $this->saleService->getSales();

        if (request()->keyword !== null) {
            $sales = $sales->where(function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('name', 'like', '%' . request()->keyword . '%')
                        ->orWhere('email', 'like', '%' . request()->keyword . '%')
                        ->orWhere('phone', 'like', '%' . request()->keyword . '%')
                        ->orWhere('address', 'like', '%' . request()->keyword . '%');
                })->orWhere('invoice', 'like', '%' . request()->keyword . '%');
            });
        }

        // Filter by product
        if (request()->product_id) {
            $sales = $sales->whereHas('details', function ($q) {
                $q->where('product_id', request('product_id'));
            });
        }

        // Filter by customer
        if (request()->customer || request()->customer_id) {
            $sales = $sales->where('customer_id', request('customer') ?? request('customer_id'));
        }

        $fromDate = request('from_date') ? now()->parse(request('from_date'))->format('Y-m-d') : '';
        $toDate = request('to_date') ? now()->parse(request('to_date'))->format('Y-m-d') : date('Y-m-d');


        // from date and to date
        if (request('from_date') || request('to_date')) {
            $sales = $sales->whereBetween('order_date', [$fromDate, $toDate]);
        }
        $sort = request()->order_by ? request()->order_by : 'desc';
        $sales = $sales->orderBy('order_date', $sort)->orderBy('invoice', $sort);

        $data['sale_amount'] = 0;
        $data['discount_amount'] = 0;
        $data['total_amount'] = 0;
        $data['income_amount'] = 0;
        $data['paid_amount'] = 0;
        $data['due_amount'] = 0;

        $customerAdvMap = [];
        $advanceOffsets = [];
        $hasDateFilter = request('from_date') || request('to_date');

        foreach ($sales->get() as $sale) {
            $cid = $sale->customer_id;
            if ($cid && !isset($customerAdvMap[$cid])) {
                $customerAdvMap[$cid] = $sale->customer
                    ? ($hasDateFilter ? $sale->customer->trueAdvances() : $sale->customer->advances())
                    : 0;
            }
            $offset = 0;
            if ($cid && isset($customerAdvMap[$cid])) {
                $offset = min(max(0, $sale->due_amount), max(0, $customerAdvMap[$cid]));
                $customerAdvMap[$cid] -= $offset;
            }
            $advanceOffsets[$sale->id] = $offset;

            $returnAmount = $sale->saleReturns->sum('return_amount');
            $returnDue = max(0, $sale->saleReturns->sum('return_due'));
            $outsideIncome = $sale->details->where('source', 2)->sum(function ($d) {
                return ($d->price - $d->purchase_price) * $d->quantity;
            });
            $data['sale_amount'] += $sale->total_price;
            $data['discount_amount'] += $sale->order_discount;
            $data['total_amount'] += $sale->grand_total - $returnAmount;
            $data['income_amount'] += $outsideIncome;
            $data['paid_amount'] += $sale->paid_amount + $offset;
            $data['due_amount'] += max(0, $sale->due_amount - $offset - $returnDue);
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

        if (checkAdminHasPermission('sales.excel.download')) {
            if (request('export')) {
                $fileName = 'sales-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new SalesExport($sales), $fileName);
            }
        }

        if (checkAdminHasPermission('sales.pdf.download')) {
            if (request('export_pdf')) {
                return view('sales::pdf.sales', [
                    'sales' => $sales,
                ]);
            }
        }

        $title = 'Sales List';
        $products = Product::where('status', 1)->orderBy('id', 'desc')->get();
        $customers = User::where('status', 1)->orderBy('name', 'asc')->get();
        return view('sales::index', compact('sales', 'title', 'data', 'products', 'customers', 'advanceOffsets'));
    }

    public function serviceSales()
    {
        checkAdminHasPermissionAndThrowException('sales.view');
        $sales = $this->saleService->getSales();

        // Only sales that contain services
        $sales = $sales->whereHas('services');

        if (request()->keyword !== null) {
            $sales = $sales->where(function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('name', 'like', '%' . request()->keyword . '%')
                        ->orWhere('email', 'like', '%' . request()->keyword . '%')
                        ->orWhere('phone', 'like', '%' . request()->keyword . '%')
                        ->orWhere('address', 'like', '%' . request()->keyword . '%');
                })->orWhere('invoice', 'like', '%' . request()->keyword . '%');
            });
        }

        // Filter by service
        if (request()->service_id) {
            $sales = $sales->whereHas('services', function ($q) {
                $q->where('service_id', request('service_id'));
            });
        }

        // Filter by customer
        if (request()->customer) {
            $sales = $sales->where('customer_id', request('customer'));
        }

        $fromDate = request('from_date') ? now()->parse(request('from_date'))->format('Y-m-d') : '';
        $toDate = request('to_date') ? now()->parse(request('to_date'))->format('Y-m-d') : date('Y-m-d');

        if (request('from_date') || request('to_date')) {
            $sales = $sales->whereBetween('order_date', [$fromDate, $toDate]);
        }

        $sort = request()->order_by ? request()->order_by : 'desc';
        $sales = $sales->orderBy('order_date', $sort)->orderBy('invoice', $sort);

        $data['service_amount'] = 0;
        $data['service_qty'] = 0;

        foreach ($sales->get() as $sale) {
            foreach ($sale->services as $serviceSale) {
                $data['service_amount'] += $serviceSale->sub_total;
                $data['service_qty'] += $serviceSale->quantity;
            }
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

        $title = 'Service Sales List';
        $servicesList = \Modules\Service\app\Models\Service::where('status', 1)->orderBy('name', 'asc')->get();
        $customers = User::where('status', 1)->orderBy('name', 'asc')->get();
        return view('sales::service-sales', compact('sales', 'title', 'data', 'servicesList', 'customers'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        checkAdminHasPermissionAndThrowException('sales.view');
        $sale = $this->saleService->getSales()->find($id);
        return view('sales::view-modal', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('sales.edit');

        session()->forget('UPDATE_CART');
        [$cart_contents, $sale] = $this->saleService->editSale($id);
        $products = Product::where('status', 1)->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->orderBy('id', 'desc');

        if ($request->category_id) {
            $products = $products->where(function ($query) use ($request) {
                $query->where('category_id', $request->category_id)->where('status', 1);
            });
        }

        if ($request->name) {
            $products = $products->whereHas('translations', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->name . '%');
            });
        }

        $products = $products->paginate(5);

        $products = $products->appends($request->all());

        $categories = Category::where('status', 1)->get();
        $brands = $this->brandService->getActiveBrands();
        $customers = User::orderBy('name', 'asc')->where('status', 1)->with(['payment', 'sales'])->get();
        $accounts = Account::with('bank')->get();
        $groups = $this->userGroup->getUserGroup()->where('type', 'customer')->where('status', 1)->get();
        $areaList = $this->areaService->getArea()->get();
        $vehicles = $this->vehicle->get();

        $services = $this->services->all()->where('status', 1)->paginate(20);
        $services->appends(request()->query());

        $serviceCategories = $this->services->getCategories();

        $cart_holds = CartHold::where('status', 'hold')->orderBy('id', 'desc')->get();
        return view('sales::edit')->with([
            'products' => $products,
            'categories' => $categories,
            'customers' => $customers,
            'cart_contents' => $cart_contents,
            'brands' => $brands,
            'groups' => $groups,
            'accounts' => $accounts,
            'areaList' => $areaList,
            'vehicles' => $vehicles,
            'services' => $services,
            'cart_holds' => $cart_holds,
            'serviceCategories' => $serviceCategories,
            'sale' => $sale
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        checkAdminHasPermissionAndThrowException('sales.edit');
        $user = null;
        if ($request->order_customer_id && $request->order_customer_id !=  'walk-in-customer') {

            Validator::make($request->all(), [
                'order_customer_id' => 'required',
            ], [
                'order_customer_id.required' => trans('Customer is required'),
            ])->validate();

            $user = User::find($request->order_customer_id);
            if (!$user) {
                return response()->json([
                    'message' => trans('Customer not found'),
                    'alert-type' => 'error',
                ], 422);
            }
        }

        // Prevent due sales for walk-in/guest customers
        if ($request->order_customer_id == 'walk-in-customer') {
            $totalPaid = array_sum($request->paying_amount ?? []);
            $totalAmount = floatval($request->total_amount ?? 0);
            if ($totalPaid < $totalAmount) {
                return response()->json([
                    'message' => trans("Can't Make Due Sale for Guest Customer"),
                    'alert-type' => 'error',
                ], 422);
            }
        }

        $cart = session('UPDATE_CART');

        DB::beginTransaction();
        try {
            $order = $this->saleService->updateSale($request, $user,  $cart, $id);
            DB::commit();
            session()->put('UPDATE_CART', []);
            return response()->json([
                'order' => $order,
                'message' => 'Order Updated successfully. Please wait 30 seconds before submitting another request.',
                'alert-type' => 'success',
            ], 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());

            return response()->json([
                'message' => $ex->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('sales.delete');
        try {
            $this->saleService->deleteSale($id);
            return back()->with(['alert-type' => 'success', 'messege' => 'Sale deleted successfully']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return back()->with(['alert-type' => 'danger', 'messege' => 'Something went wrong!']);
        }
    }

    public function invoice($id)
    {
        checkAdminHasPermissionAndThrowException('sales.invoice');
        $sale = $this->saleService->getSales()->find($id);
        return view('sales::invoice', compact('sale'));
    }
}
