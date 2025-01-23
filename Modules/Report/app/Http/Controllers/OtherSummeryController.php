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
        checkAdminHasPermissionAndThrowException('customer.other.due.view');
        $fromDate = request('from_date') ? now()->parse(request('from_date'))->format('Y-m-d') : '';
        $toDate = request('to_date') ? now()->parse(request('to_date'))->format('Y-m-d') : '';
        $customers = User::all();

        $summeries = User::whereHas('otherSummery');


        if ($fromDate || $toDate) {
            $summeries = $summeries->whereHas('otherSummery', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('date', [$fromDate, $toDate]);
            });
        }
        if (request()->keyword) {
            $summeries = $summeries->where(function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%')
                    ->orWhere('phone', 'like', '%' . request()->keyword . '%');
            });
        }

        $summeriesData = $summeries->with('otherSummery')->get();
        $data['total_amount'] = $summeriesData->sum(function ($user) {
            return $user->otherSummery->sum('amount');
        });
        $data['total_paid'] = $summeriesData->sum(function ($user) {
            return $user->otherSummery->sum('paid');
        });
        $data['total_due'] = $summeriesData->sum(function ($user) {
            return $user->otherSummery->sum('due');
        });

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $summeries = $summeries->get();
        } else {
            $summeries = $summeries->paginate($parpage);
            $summeries->appends(request()->query());
        }

        if (checkAdminHasPermission('customer.other.due.pdf.download')) {
            if (request('export_pdf')) {

                return view('report::pdf.customer-due', [
                    'summeries' => $summeries
                ]);
            }
        }
        return view('report::other-summery.customer', compact('customers', 'summeries', 'data'));
    }

    public function customerStore(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.other.due.create');
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

    public function customerLedger($id)
    {
        checkAdminHasPermissionAndThrowException('customer.other.due.ledger');
        $fromDate = request('from_date') ? now()->parse(request('from_date'))->format('Y-m-d') : '';
        $toDate = request('to_date') ? now()->parse(request('to_date'))->format('Y-m-d') : '';
        $customers = User::all();
        $summeries = OtherSummery::where('customer_id', $id);

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

        if (request('par-page')) {
            $perpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $perpage = 20;
        }

        $data['total_amount'] = $summeries->sum('amount');
        $data['total_paid'] = $summeries->sum('paid');
        $data['total_due'] = $summeries->sum('due');


        $summeries = $summeries->paginate($perpage);
        $summeries->appends(request()->all());

        return view('report::other-summery.customer-ledger', compact('summeries', 'data'));
    }
    public function supplierLedger($id)
    {
        checkAdminHasPermissionAndThrowException('supplier.other.due.ledger');
        $fromDate = request('from_date') ? now()->parse(request('from_date'))->format('Y-m-d') : '';
        $toDate = request('to_date') ? now()->parse(request('to_date'))->format('Y-m-d') : '';
        $customers = User::all();
        $summeries = OtherSummery::where('supplier_id', $id);

        if ($fromDate || $toDate) {
            $summeries = $summeries->whereBetween('date', [$fromDate, $toDate]);
        }
        if (request()->keyword) {
            $summeries = $summeries->where(function ($q) {
                $q->whereHas('supplier', function ($q) {
                    $q->where('name', 'like', '%' . request()->keyword . '%')
                        ->orWhere('phone', 'like', '%' . request()->keyword . '%');
                });
            });
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $summeries = $summeries->get();
        } else {
            $summeries = $summeries->paginate($parpage);
            $summeries->appends(request()->query());
        }

        if (checkAdminHasPermission('customer.other.due.pdf.download')) {
            if (request('export_pdf')) {

                return view('report::pdf.supplier-due', [
                    'summeries' => $summeries,
                ]);
            }
        }

        $data['total_amount'] = $summeries->sum('amount');
        $data['total_paid'] = $summeries->sum('paid');
        $data['total_due'] = $summeries->sum('due');


        return view('report::other-summery.supplier-ledger', compact('summeries', 'data'));
    }

    public function payDue(Request $request)
    {
        if (!checkAdminHasPermission('customer.other.due.pay') || !checkAdminHasPermission('supplier.other.due.pay')) {
            return abort(403);
        }
        $request->validate([
            'amount' => 'required',
            'customer_id' => 'required_if:supplier_id,null',
            'supplier_id' => 'required_if:customer_id,null',
        ]);

        $summery = OtherSummery::where('customer_id', $request->customer_id)->where('supplier_id', $request->supplier_id)->get();


        $amount = $request->amount;
        foreach ($summery as $key => $value) {
            if ($amount <= $value->due) {
                if ($amount > 0) {
                    $value->paid += $amount;
                    $value->due -= $amount;
                    $amount = 0;
                }
            } else {
                if ($value->due > 0 && $amount > $value->due) {
                    $value->paid += $value->due;
                    $amount -= $value->due;
                    $value->due = 0;
                }
            }
            $value->save();
        }

        return redirect()->back()->with(['alert-type' => 'success', 'messege' => 'Due Pay successfully']);
    }
    public function customerUpdate(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('customer.other.due.edit');
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
        checkAdminHasPermissionAndThrowException('customer.other.due.delete');
        $summery =  OtherSummery::find($id);
        $summery->delete();
        return redirect()->back()->with(['alert-type' => 'success', 'messege' => 'Customer due summery deleted successfully']);
    }

    public function supplier()
    {
        checkAdminHasPermissionAndThrowException('supplier.other.due.view');
        $fromDate = request('from_date') ? now()->parse(request('from_date'))->format('Y-m-d') : '';
        $toDate = request('to_date') ? now()->parse(request('to_date'))->format('Y-m-d') : '';
        $suppliers = Supplier::all();
        $summeries = Supplier::whereHas('otherSummery');

        if ($fromDate || $toDate) {
            $summeries = $summeries->whereHas('otherSummery', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('date', [$fromDate, $toDate]);
            });
        }
        if (request()->keyword) {
            $summeries = $summeries->where(function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%')
                    ->orWhere('phone', 'like', '%' . request()->keyword . '%');
            });
        }

        $summeriesData = $summeries->with('otherSummery')->get();
        $data['total_amount'] = $summeriesData->sum(function ($user) {
            return $user->otherSummery->sum('amount');
        });
        $data['total_paid'] = $summeriesData->sum(function ($user) {
            return $user->otherSummery->sum('paid');
        });
        $data['total_due'] = $summeriesData->sum(function ($user) {
            return $user->otherSummery->sum('due');
        });



        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $summeries = $summeries->get();
        } else {
            $summeries = $summeries->paginate($parpage);
            $summeries->appends(request()->query());
        }
        if (checkAdminHasPermission('supplier.other.due.pdf.download')) {
            if (request('export_pdf')) {

                return view('report::pdf.supplier-due', [
                    'summeries' => $summeries,
                ]);
            }
        }
        return view('report::other-summery.supplier', compact('suppliers', 'summeries', 'data'));
    }


    public function supplierStore(Request $request)
    {
        checkAdminHasPermissionAndThrowException('supplier.other.due.create');
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
        checkAdminHasPermissionAndThrowException('supplier.other.due.edit');
        $request->validate([
            'supplier_id' => 'required',
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
        checkAdminHasPermissionAndThrowException('supplier.other.due.delete');
        $summery =  OtherSummery::find($id);
        $summery->delete();
        return redirect()->back()->with(['alert-type' => 'success', 'messege' => 'Supplier due summery deleted successfully']);
    }
}
