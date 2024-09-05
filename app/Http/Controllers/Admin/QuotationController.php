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
        $quotations = Quotation::query();

        if (request()->keyword) {
            $quotations->where(function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('name', 'like', '%' . request()->keyword . '%');
                })
                    ->orWhere('quotation_no', 'like', '%' . request()->keyword . '%')
                ;
            });
        }

        $quotations = $quotations->orderBy('id', 'desc')->paginate(20);

        return view('admin.pages.quotation.index', compact('quotations'));
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
        $request->validate([
            'customer_id' => 'required',
            'date' => 'required',
            'product_id' => 'required|array',
            'product_id.*' => 'required',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required',
            'quantity' => 'required|array',
            'quantity.*' => 'required',
        ]);
        DB::beginTransaction();

        try {

            // check quotation no
            // last quotation no
            $quotation_no = Quotation::orderBy('id', 'desc')->first();
            $quotation_no = $quotation_no ? $quotation_no->quotation_no + 1 : 1;

            // create quotation

            $quotation = Quotation::create([
                'customer_id' => $request->customer_id,
                'date' => now()->parse($request->date),
                'note' => $request->note,
                'subtotal' => $request->subtotal ?? 0,
                'discount' => $request->discount ?? 0,
                'after_discount' => $request->after_discount ?? 0,
                'vat' => $request->vat ?? 0,
                'total' => $request->total_amount ?? 0,
                'created_by' => auth('admin')->user()->id,
                'quotation_no' => $quotation_no,
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
        $quotation = Quotation::find($id);
        return view('admin.pages.quotation.show', compact('quotation'));
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
