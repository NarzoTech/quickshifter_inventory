<?php

namespace Modules\Sales\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Accounts\app\Models\Account;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Customer\app\Http\Services\UserGroupService;
use Modules\Customer\app\Models\Vehicle;
use Modules\POS\app\Models\CartHold;
use Modules\Product\app\Models\Category;
use Modules\Product\app\Models\Product;
use Modules\Product\app\Services\BrandService;
use Modules\Sales\app\Models\Sale;
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
        $sales = $this->saleService->getSales();

        if (request()->customer) {
            $sales = $sales->where('customer_id', request()->customer);
        }
        $sales = $sales->orderBy('id', 'desc')->paginate(20);
        $title = 'Sales List';
        return view('sales::index', compact('sales', 'title'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $sale = $this->saleService->getSales()->find($id);
        return view('sales::view-modal', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
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
        $customers = User::orderBy('id', 'desc')->where('status', 1)->get();
        $accounts = Account::with('bank')->get();
        $groups = $this->userGroup->getUserGroup()->where('type', 'customer')->where('status', 1)->get();
        $areaList = $this->areaService->getArea()->get();
        $vehicles = $this->vehicle->get();

        $services = $this->services->all()->where('status', 1)->paginate(20);
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
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->saleService->deleteSale($id);
            return back()->with(['alert-type' => 'success', 'messege' => 'Sale deleted successfully']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return back()->with(['alert-type' => 'danger', 'messege' => 'Something went wrong!']);
        }
    }
}
