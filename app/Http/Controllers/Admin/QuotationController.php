<?php

namespace App\Http\Controllers\Admin;

use App\Exports\QuotationExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotationRequest;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
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
        checkAdminHasPermissionAndThrowException('quotation.view');
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



        $fromDate = request('from_date') ? now()->parse(request('from_date'))->subDay()->format('Y-m-d') : '';
        $toDate = request('to_date') ? now()->parse(request('to_date'))->format('Y-m-d') : date('Y-m-d');

        // from date and to date
        if ($fromDate) {
            $quotations = $quotations->whereBetween('date', [$fromDate, $toDate]);
        }
        $sort = request()->order_by ? request()->order_by : 'desc';
        $quotations = $quotations->orderBy('date', $sort);


        $data['total'] = $quotations->sum('total');

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $quotations = $quotations->get();
        } else {
            $quotations = $quotations->paginate($parpage);
            $quotations->appends(request()->query());
        }

        if (checkAdminHasPermission('quotation.excel.download')) {
            if (request('export')) {
                $fileName = 'quotation-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new QuotationExport($quotations), $fileName);
            }
        }
        if (checkAdminHasPermission('quotation.pdf.download')) {
            if (request('export_pdf')) {
                return view('admin.pages.quotation.pdf.quotation', [
                    'quotations' => $quotations,
                ]);
            }
        }


        return view('admin.pages.quotation.index', compact('quotations', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('quotation.create');
        $customers = User::orderBy('name', 'asc')->where('status', 1)->get();
        $products = Product::where('status', 1)->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->orderBy('name', 'asc')->get();
        return view('admin.pages.quotation.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuotationRequest $request)
    {
        checkAdminHasPermissionAndThrowException('quotation.create');
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
                'expiry_date' => $request->expiry_date ? now()->parse($request->expiry_date) : null,
                'note' => $request->note,
                'subtotal' => $request->subtotal ?? 0,
                'discount' => $request->discount ?? 0,
                'after_discount' => $request->after_discount ?? 0,
                'vat' => $request->vat ?? 0,
                'total' => $request->total_amount ?? 0,
                'created_by' => auth('admin')->user()->id,
                'quotation_no' => $quotation_no,
                'status' => $request->status ?? 'draft',
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
        checkAdminHasPermissionAndThrowException('quotation.view');
        $quotation = Quotation::find($id);
        return view('admin.pages.quotation.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('quotation.edit');
        $quotation = Quotation::find($id);
        $customers = User::orderBy('name', 'asc')->where('status', 1)->get();
        $products = Product::where('status', 1)->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->orderBy('name', 'asc')->get();
        return view('admin.pages.quotation.edit', compact('quotation', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('quotation.edit');
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
            $quotation = Quotation::find($id);
            $quotation->update([
                'customer_id' => $request->customer_id,
                'date' => now()->parse($request->date),
                'expiry_date' => $request->expiry_date ? now()->parse($request->expiry_date) : null,
                'note' => $request->note,
                'subtotal' => $request->subtotal ?? 0,
                'discount' => $request->discount ?? 0,
                'after_discount' => $request->after_discount ?? 0,
                'vat' => $request->vat ?? 0,
                'total' => $request->total_amount ?? 0,
                'updated_by' => auth('admin')->user()->id,
                'status' => $request->status ?? $quotation->status,
            ]); // update quotation

            $quotation->details()->delete();
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
                'messege' => 'Quotation Updated Successfully'
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('quotation.delete');
        $quotation = Quotation::find($id);
        $quotation->details()->delete();
        $quotation->delete();
        return redirect()->back()->with([
            'alert-type' => 'success',
            'messege' => 'Quotation Deleted Successfully'
        ]);
    }

    /**
     * Convert quotation to sale
     */
    public function convertToSale(string $id)
    {
        checkAdminHasPermissionAndThrowException('sale.create');
        
        $quotation = Quotation::with('details.product')->findOrFail($id);
        
        // Check if quotation is expired
        if ($quotation->isExpired()) {
            return redirect()->back()->with([
                'alert-type' => 'error',
                'messege' => 'Cannot convert expired quotation to sale'
            ]);
        }
        
        // Check if quotation is already accepted or rejected
        if (in_array($quotation->status, ['rejected'])) {
            return redirect()->back()->with([
                'alert-type' => 'error',
                'messege' => 'Cannot convert rejected quotation to sale'
            ]);
        }
        
        // Redirect to sale create page with quotation data pre-filled
        $products = [];
        foreach ($quotation->details as $detail) {
            $products[] = [
                'product_id' => $detail->product_id,
                'product_name' => $detail->product->name,
                'quantity' => $detail->quantity,
                'price' => $detail->price,
                'sub_total' => $detail->sub_total,
            ];
        }
        
        return redirect()->route('admin.sales.create')->with([
            'quotation_data' => [
                'quotation_id' => $quotation->id,
                'customer_id' => $quotation->customer_id,
                'products' => $products,
                'subtotal' => $quotation->subtotal,
                'discount' => $quotation->discount,
                'after_discount' => $quotation->after_discount,
                'vat' => $quotation->vat,
                'total' => $quotation->total,
                'note' => $quotation->note,
            ],
            'alert-type' => 'info',
            'messege' => 'Quotation data loaded. Complete the sale details below.'
        ]);
    }
}
