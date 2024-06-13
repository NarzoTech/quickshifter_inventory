<?php

namespace Modules\Supplier\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Customer\app\Http\Services\UserGroupService;
use Modules\Supplier\app\Services\SupplierService;

class SupplierController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private SupplierService $supplierService, private UserGroupService $userGroup, private AreaService $areaService)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = $this->supplierService->all()->paginate(20);
        $groups = $this->userGroup->getUserGroup()->where('type', 'supplier')->where('status', 1)->get();
        $areaList = $this->areaService->getArea()->get();
        return view('supplier::index', compact('suppliers', 'groups', 'areaList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);

        try {
            $this->supplierService->storeSupplier($request);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier created successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier creation failed.', 'alert-type' => 'error']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('supplier::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        return view('supplier::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required']);

        try {
            $this->supplierService->updateSupplier($request, $id);
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier updated successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier update failed.', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->supplierService->deleteSupplier($id);
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier deleted successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier deletion failed.', 'alert-type' => 'error']);
        }
    }


    public function duePay($id)
    {
        $supplier = $this->supplierService->find($id)->with('duePurchase')->first();
        return view('supplier::due-pay', compact('supplier'));
    }
}
