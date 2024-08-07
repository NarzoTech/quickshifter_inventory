<?php

namespace Modules\Sales\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Services\SaleService;

class SalesController extends Controller
{
    public function __construct(private SaleService $saleService)
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sales::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
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
    public function edit($id)
    {
        return view('sales::edit');
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
