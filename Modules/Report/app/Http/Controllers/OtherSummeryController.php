<?php

namespace Modules\Report\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Report\app\Models\OtherSummery;
use Modules\Supplier\app\Models\Supplier;

class OtherSummeryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function customer()
    {
        $fromDate = request('from_date') ? now()->parse(request('from_date'))->format('Y-m-d') : '';
        $toDate = request('to_date') ? now()->parse(request('to_date'))->format('Y-m-d') : '';
        $customers = User::all();
        $summeries =  OtherSummery::with('customer')->whereNotNull('customer_id');

        if ($fromDate || $toDate) {
            $summeries = $summeries->whereBetween('date', [$fromDate, $toDate]);
        }
        if (request()->keyword) {
            $summeries = $summeries->where(function ($q) {
                $q->whereHas('customer', function ($q) {
                    $q->where('name', 'like', '%' . request()->keyword . '%')
                        ->orWhere('phone', 'like', '%' . request()->keyword . '%');
                });
            });
        }

        $data['total_amount'] = $summeries->sum('amount');
        $data['total_paid'] = $summeries->sum('paid');
        $data['total_due'] = $summeries->sum('due');


        $summeries = $summeries->paginate(20);
        $summeries->appends(request()->all());
        return view('report::other-summery.customer', compact('customers', 'summeries', 'data'));
    }

    public function customerStore(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required',
            'paid' => 'required',
            'due' => 'required',
        ]);

        $customer = User::find($request->customer_id);
        if (!$customer) {
            return redirect()->back()->with(['alert-type' => 'error', 'messege' => 'Customer not found']);
        }

        $summery =  OtherSummery::create([
            'customer_id' => $request->customer_id,
            'date' => now()->parse($request->date),
            'amount' => $request->amount,
            'paid' => $request->paid,
            'due' => $request->due,
            'description' => $request->description,
        ]);
        if ($summery) {
            return redirect()->back()->with(['alert-type' => 'success', 'messege' => 'Customer due summery created successfully']);
        } else {
            return redirect()->back()->with(['alert-type' => 'error', 'messege' => 'Something went wrong']);
        }
    }

    public function customerUpdate(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required',
            'paid' => 'required',
            'due' => 'required',
        ]);
        $summery =  OtherSummery::find($id);

        $data = $request->except('_token');
        $data['date'] = now()->parse($request->date);
        $summery->update($data);
        return redirect()->back()->with(['alert-type' => 'success', 'messege' => 'Customer due summery updated successfully']);
    }

    public function customerDelete($id)
    {
        $summery =  OtherSummery::find($id);
        $summery->delete();
        return redirect()->back()->with(['alert-type' => 'success', 'messege' => 'Customer due summery deleted successfully']);
    }

    public function supplier()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date'))->format('Y-m-d') : '';
        $toDate = request('to_date') ? now()->parse(request('to_date'))->format('Y-m-d') : '';
        $suppliers = Supplier::all();
        $summeries =  OtherSummery::with('supplier')->whereNotNull('supplier_id');

        if ($fromDate || $toDate) {
            $summeries = $summeries->whereBetween('date', [$fromDate, $toDate]);
        }
        if (request()->keyword) {
            $summeries = $summeries->where(function ($q) {
                $q->whereHas('supplier', function ($q) {
                    $q->where('name', 'like', '%' . request()->keyword . '%')
                        ->orWhere('phone', 'like', '%' . request()->keyword . '%')
                        ->orWhere('company', 'like', '%' . request()->keyword . '%');
                })
                    ->orWhere('memo_number', 'like', '%' . request()->keyword . '%')
                ;
            });
        }

        $data['total_amount'] = $summeries->sum('amount');
        $data['total_paid'] = $summeries->sum('paid');
        $data['total_due'] = $summeries->sum('due');


        $summeries = $summeries->paginate(20);
        $summeries->appends(request()->all());
        return view('report::other-summery.supplier', compact('suppliers', 'summeries', 'data'));
    }


    public function supplierStore(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required',
            'paid' => 'required',
            'due' => 'required',
        ]);
        $supplier = Supplier::find($request->supplier_id);
        if (!$supplier) {
            return redirect()->back()->with(['alert-type' => 'error', 'messege' => 'Supplier not found']);
        }

        $summery =  OtherSummery::create([
            'supplier_id' => $request->supplier_id,
            'date' => now()->parse($request->date),
            'amount' => $request->amount,
            'paid' => $request->paid,
            'due' => $request->due,
            'description' => $request->description,
            'memo_number' => $request->memo_number,
        ]);
        if ($summery) {
            return redirect()->back()->with(['alert-type' => 'success', 'messege' => 'Supplier due summery created successfully']);
        } else {
            return redirect()->back()->with(['alert-type' => 'error', 'messege' => 'Something went wrong']);
        }
    }

    public function supplierUpdate(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required',
            'paid' => 'required',
            'due' => 'required',
        ]);
        $summery =  OtherSummery::find($id);

        $data = $request->except('_token');
        $data['date'] = now()->parse($request->date);
        $summery->update($data);
        return redirect()->back()->with(['alert-type' => 'success', 'messege' => 'Supplier due summery updated successfully']);
    }

    public function  supplierDelete($id)
    {
        $summery =  OtherSummery::find($id);
        $summery->delete();
        return redirect()->back()->with(['alert-type' => 'success', 'messege' => 'Supplier due summery deleted successfully']);
    }
}
