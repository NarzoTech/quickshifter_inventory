<?php

namespace Modules\Supplier\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Customer\app\Http\Services\UserGroupService;
use Modules\Supplier\app\Services\SupplierService;

class SupplierController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private SupplierService $supplierService, private UserGroupService $userGroup, private AreaService $areaService, private AccountsService $accountsService)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = $this->supplierService->allSupplier()->paginate(20);
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
        $accounts = $this->accountsService->all()->with('bank')->get();
        return view('supplier::due-pay', compact('supplier', 'accounts'));
    }

    public function duePayStore(Request $request, $id)
    {
        $rule = [
            'invoice_no' => 'required|array',
            'invoice_no.*' => 'required',
            'amount' => 'required|array',
            'amount.*' => 'required',
            'payment_date' => 'required|date',
            'paying_amount' => 'required',
            'payment_type' => 'required',
        ];

        $message = [
            'invoice_no.required' => 'Invoice number is required',
            'amount.required' => 'Amount is required',
            'date.required' => 'Date is required',
            'paying_amount.required' => 'Paying amount is required',
            'payment_type.required' => 'Payment type is required',
        ];

        $request->validate($rule, $message);
        DB::beginTransaction();
        try {
            $this->supplierService->duePay($request, $id);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.suppliers.index', [], ['messege' => 'Due payment successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.suppliers.index', [], ['messege' => 'Due payment failed.', 'alert-type' => 'error']);
        }
    }

    public function duePayHistory()
    {
        $payments = $this->supplierService->duePayHistory();
        return view('supplier::due-pay-history', compact('payments'));
    }

    public function changeStatus($id)
    {
        $supplier = $this->supplierService->find($id);

        $status = $supplier->status == 1 ? 0 : 1;

        $supplier->status = $status;
        $supplier->save();

        $notification = $status == 1 ? 'Supplier activated' : 'Supplier deactivated';

        return response()->json(['status' => 'success', 'message' => $notification]);
    }

    public function advance($id)
    {
        $supplier = $this->supplierService->find($id);
        $accounts = $this->accountsService->all()->with('bank')->get();
        return view('supplier::advance', compact('supplier', 'accounts'));
    }

    public function advanceStore(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'advance' => 'nullable',
            'paying_amount' => 'nullable',
            'refund_amount' => 'nullable',
            'date' => 'required',
            'total_amount' => 'required',
            'payment_type' => 'required',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (is_null($request->paying_amount) && is_null($request->refund_amount)) {
                $validator->errors()->add('paying_amount', 'Either paying_amount or refund_amount must be provided.');
                $validator->errors()->add('refund_amount', 'Either paying_amount or refund_amount must be provided.');
            } elseif (!is_null($request->paying_amount) && !is_null($request->refund_amount)) {
                $validator->errors()->add('paying_amount', 'Only one of paying_amount or refund_amount can be provided.');
                $validator->errors()->add('refund_amount', 'Only one of paying_amount or refund_amount can be provided.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $this->supplierService->advancePay($request, $id);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.suppliers.index', [], ['messege' => 'Advance payment successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.suppliers.index', [], ['messege' => 'Advance payment failed.', 'alert-type' => 'error']);
        }
    }

    public function ledger($id)
    {
        $supplier = $this->supplierService->find($id);

        $ledgers = Ledger::where('supplier_id', $supplier->id)->orderBy('date', 'desc')->paginate(20);
        return view('supplier::ledger', compact('supplier', 'ledgers'));
    }
}
