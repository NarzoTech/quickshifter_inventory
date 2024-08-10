<?php

namespace Modules\Purchase\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Purchase\app\Http\Requests\PurchaseRequest;
use Modules\Purchase\app\Models\PurchaseDetails;
use Modules\Purchase\app\Services\PurchaseService;

class PurchaseController extends Controller
{

    use RedirectHelperTrait;
    public function __construct(private PurchaseService $purchaseService, private PurchaseDetails $purchaseDetails)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = $this->purchaseService->all()->paginate(20);
        return view('purchase::index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $suppliers = $this->purchaseService->getSuppliers();
        $warehouses = $this->purchaseService->getWarehouses();
        $products = $this->purchaseService->getProducts($request);
        $invoiceNumber = $this->purchaseService->genInvoiceNumber();
        $accounts = $this->purchaseService->getAccounts();
        return view('purchase::create', compact('suppliers', 'warehouses', 'products', 'invoiceNumber', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PurchaseRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $this->purchaseService->store($request);

            DB::commit();

            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.purchase.index', [], ['messege' => 'Product Purchase successfully', 'alert-type' => 'success']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('purchase::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $suppliers = $this->purchaseService->getSuppliers();
        $warehouses = $this->purchaseService->getWarehouses();
        $products = $this->purchaseService->getProducts($request);
        $invoiceNumber = $this->purchaseService->genInvoiceNumber();
        $accounts = $this->purchaseService->getAccounts();
        $purchase = $this->purchaseService->getPurchase($id);
        return view('purchase::edit', compact('suppliers', 'warehouses', 'products', 'invoiceNumber', 'purchase', 'accounts'));
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
        //
    }
}
