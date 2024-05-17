<?php

namespace Modules\Purchase\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        return view('purchase::create', compact('suppliers', 'warehouses', 'products', 'invoiceNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        dd($request->all());
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
    public function edit($id)
    {
        return view('purchase::edit');
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
