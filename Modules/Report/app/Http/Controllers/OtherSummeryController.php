<?php

namespace Modules\Report\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Report\app\Models\OtherSummery;

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
        $summeries =  OtherSummery::with('customer');

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
        return view('report::other-summery.customer', compact('customers', 'summeries', 'data'));
    }

    public function customerStore(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'date' => 'required|date',
            'amount' => 'required',
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('report::create');
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
        return view('report::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('report::edit');
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
