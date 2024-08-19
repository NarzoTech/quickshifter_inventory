<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuotationRequest;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Product\app\Models\Product;

class QuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = User::orderBy('id', 'desc')->where('status', 1)->get();
        $products = Product::where('status', 1)->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->orderBy('id', 'desc')->get();
        return view('admin.pages.quotation.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuotationRequest $request)
    {
        DB::beginTransaction();

        try {
            // create quotation

            $quotation = Quotation::create([
                'customer_id' => $request->customer_id,
                'date' => now()->parse($request->date),
                'note' => $request->note,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount,
                'after_discount' => $request->after_discount,
                'vat' => $request->vat,
                'total' => $request->total_amount,
                'created_by' => auth('admin')->user()->id,
                // 'warehouse_id' => $request->warehouse_id,
            ]);


            // create quotation details
            foreach ($request->product_id as $key => $product_id) {

                $quotation->details()->create([
                    'product_id' => $product_id,
                    'quantity' => $request->quantity[$key],
                    'price' => $request->unit_price[$key],
                    'sub_total' => $request->total[$key],
                ]);
            }


            DB::commit();
            return redirect()->route('admin.quotation.index')->with([
                'alert-type' => 'success',
                'messege' => 'Quotation created successfully'
            ]);
        } catch (\Exception $ex) {

            DB::rollBack();
            Log::error($ex->getMessage());
            return redirect()->back()->with([
                'alert-type' => 'error',
                'messege' => $ex->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
