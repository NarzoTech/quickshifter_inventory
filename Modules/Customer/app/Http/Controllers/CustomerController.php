<?php

namespace Modules\Customer\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\Payment;
use App\Models\User;
use App\Services\MailSenderService;
use App\Traits\GetGlobalInformationTrait;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Accounts\app\Models\Account;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Customer\app\Http\Services\UserGroupService;
use Modules\Customer\app\Jobs\SendBulkEmailToUser;
use Modules\Customer\app\Jobs\SendUserBannedMailJob;
use Modules\Customer\app\Models\BannedHistory;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Customer\app\Models\Vehicle;
use Modules\Sales\app\Models\Sale;

class CustomerController extends Controller
{
    use RedirectHelperTrait;

    public function __construct(private UserGroupService $userGroup, private AreaService $areaService, private Vehicle $vehicle, private AccountsService $account)
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.view');

        $query = User::query();

        $query->when($request->filled('keyword'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->keyword . '%')
                ->orWhere('email', 'like', '%' . $request->keyword . '%')
                ->orWhere('phone', 'like', '%' . $request->keyword . '%')
                ->orWhere('address', 'like', '%' . $request->keyword . '%');
        });

        $orderBy = $request->filled('order_by') && $request->order_by == 1 ? 'asc' : 'desc';

        if ($request->filled('par-page')) {
            $users = $request->get('par-page') == 'all' ? $query->orderBy('id', $orderBy)->get() : $query->orderBy('id', $orderBy)->paginate($request->get('par-page'))->withQueryString();
        } else {
            $users = $query->orderBy('id', $orderBy)->paginate()->withQueryString();
        }

        $groups = $this->userGroup->getUserGroup()->where('type', 'customer')->where('status', 1)->get();
        $areaList = $this->areaService->getArea()->get();
        $vehicles = $this->vehicle->get();
        return view('customer::customer')->with([
            'users' => $users,
            'groups' => $groups,
            'areaList' => $areaList,
            'vehicles' => $vehicles,
        ]);
    }

    // store
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.create');

        $this->saveUser($request);

        // check if request is ajax
        if ($request->ajax()) {
            $customers = User::orderBy('id', 'desc')->where('status', 1)->get();
            $view = view('pos::customer-drop-down', compact('customers'))->render();
            return response()->json([
                'message' => 'Customer created successfully.',
                'alert-type' => 'success',
                'view' => $view
            ]);
        }

        return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.customers.index', [], ['messege' => 'Customer created successfully.', 'alert-type' => 'success']);
    }
    public function show($id)
    {
        checkAdminHasPermissionAndThrowException('customer.view');

        $user = User::findOrFail($id);

        $banned_histories = BannedHistory::where('user_id', $id)->orderBy('id', 'desc')->get();

        return view('customer::customer_show')->with([
            'user' => $user,
            'banned_histories' => $banned_histories,
        ]);
    }

    // update

    public function update(Request $request, $id)
    {
        // checkAdminHasPermissionAndThrowException('customer.update');

        $request->validate([
            'name' => 'required',
            'phone' => 'nullable|unique:users,phone,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $id,
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->group_id = $request->group_id;
        $user->area_id = $request->area_id;
        $user->vehicle_id = $request->vehicle_id;
        $user->membership = $request->membership;
        $user->date = now()->parse($request->date);
        $user->status = $request->status;
        $user->guest = $request->guest ? 1 : 0;
        $user->address = $request->address;
        $user->save();

        return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.customers.index', [], ['messege' => 'Customer updated successfully.', 'alert-type' => 'success']);
    }


    public function saveUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->group_id = $request->group_id;
        $user->area_id = $request->area_id;
        $user->vehicle_id = $request->vehicle_id;
        $user->membership = $request->membership;
        $user->date = now()->parse($request->date);
        $user->status = $request->status;
        $user->guest = $request->guest ? 1 : 0;
        $user->address = $request->address;
        $user->save();

        return $user;
    }

    public function singleCustomer($id)
    {
        $user = User::findOrFail($id);
        return $user;
    }

    public function dueReceiveForm(Request $request)
    {
        if (!$request->customer) {
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => 'Customer Not Found', 'alert-type' => 'error']);
        }

        $accounts = $this->account->all()->get();
        $customer = User::where('id', $request->customer)->first();

        return view('customer::due-receive', compact('customer', 'accounts'));
    }

    public function dueReceive(Request $request)
    {
        $request->validate([
            'receiving_amount' => 'required',
        ]);

        $account = $request->account_id;

        if ($account == 'cash' || $account == 'advance') {
            $account = $this->account->all()->where('account_type', $account)->first();
        } else {
            $account = $this->account->all()->find($account);
        }
        foreach ($request->invoice_no as $index => $invo) {
            $sale = Sale::where('invoice', $invo)->first();

            $sale->payment_status = $sale->due_amount == $request->amount[$index] ? 'paid' : 'due';

            $sale->paid_amount = $sale->paid_amount + $request->amount[$index];
            $sale->due_amount = $sale->due_amount - $request->amount[$index];
            $sale->save();

            // create payment data
            Payment::create([
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'account_id' => $account->id,
                'payment_type' => 'due_receive',
                'is_received' => 1,
                'amount' => $request->amount[$index],
                'payment_date' => now()->parse($request->payment_date),
                'note' => $request->note,
                'created_by' => auth('admin')->user()->id,
            ]);

            // update customer due amount
            $due = CustomerDue::where('invoice', $invo)->first();
            $due->due_amount = $due->due_amount - $request->amount[$index];
            $due->paid_amount = $due->paid_amount + $request->amount[$index];
            $due->save();
        }

        return to_route('admin.customer.due-receive.list')->with([
            'messege' => 'Customer due receive successfully.',
            'alert-type' => 'success'
        ]);
    }

    public function dueReceiveList()
    {
        $payments  = Payment::whereNotNull('sale_id')->where('payment_type', 'due_receive')->paginate(20);
        return view('customer::due-list', compact('payments'));
    }

    public function dueReceiveEdit($id)
    {
        $payment = Payment::findOrFail($id);
        $accounts = $this->account->all()->get();
        return view('customer::due-receive-edit', compact('payment', 'accounts'));
    }

    public function changeStatus($id)
    {
        $user = User::find($id);

        $status = $user->status == 1 ? 0 : 1;

        $user->status = $status;
        $user->save();

        $notification = $status == 1 ? 'Customer activated' : 'Customer deactivated';

        return response()->json(['status' => 'success', 'message' => $notification]);
    }

    public function advance($id)
    {
        $customer = User::find($id);
        $accounts = $this->account->all()->with('bank')->get();
        return view('customer::advance', compact('customer', 'accounts'));
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
                $validator->errors()->add('paying_amount', 'Either Receiving Amount or Refund Amount must be provided.');
                $validator->errors()->add('refund_amount', 'Either Receiving Amount or Refund Amount must be provided.');
            } elseif (!is_null($request->paying_amount) && !is_null($request->refund_amount)) {
                $validator->errors()->add('paying_amount', 'Only one of Receiving Amount or Refund Amount can be provided.');
                $validator->errors()->add('refund_amount', 'Only one of Receiving Amount or Refund Amount can be provided.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $this->advancePay($request, $id);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.customers.index', [], ['messege' => 'Advance payment successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.customers.index', [], ['messege' => 'Advance payment failed.', 'alert-type' => 'error']);
        }
    }


    public function advancePay(Request $request, $id)
    {
        $account = $request->account_id;

        if ($account == 'cash' || $account == 'advance') {
            $account = Account::where('account_type', $account)?->first();
        } else {
            $account = Account::find($account);
        }


        // create payment data
        CustomerPayment::create([
            'customer_id' => $id,
            'account_id' => $account->id,
            'payment_type' => $request->refund_amount != null ? 'advance_refund' : 'advance_receive',
            'is_paid' => $request->refund_amount != null ? 0 : 1,
            'is_received' => $request->refund_amount != null ? 1 : 0,
            'amount' => $request->refund_amount != null ? $request->refund_amount : $request->paying_amount,
            'account_type' => accountList()[$account->account_type],
            'note' => $request->note,
            'created_by' => auth('admin')->user()->id,
            'payment_date' => now()->parse($request->date),
            'invoice' => $this->genInvoiceNumber()
        ]);



        // create ledger

        $ledger = new Ledger();
        $ledger->customer_id = $id;
        $ledger->amount = $request->paying_amount ?? $request->refund_amount;
        $ledger->invoice_type = $request->refund_amount == null ? 'Advance Payment' : 'Payment Return';
        $ledger->is_paid = $request->refund_amount != null ? 1 : 0;
        $ledger->is_received = $request->refund_amount != null ? 0 : 1;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber();
        $ledger->note = $request->note;
        if ($request->refund_amount != null) {
            $ledger->due_amount += $request->refund_amount;
        } else {
            $ledger->due_amount -= $request->paying_amount;
        }
        $ledger->date = now()->parse($request->date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();
    }

    public function genInvoiceNumber()
    {
        $number = 001;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $purchase = CustomerPayment::latest()->first();

        if ($purchase) {
            $purchaseInvoice = $purchase->invoice;

            if ($purchaseInvoice) {
                // split the invoice number
                $split_invoice = explode('-', $purchaseInvoice);
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }


    public function genLedgerInvoiceNumber()
    {
        $number = 001;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $purchase = Ledger::where('invoice_type', 'Sale Payment')->latest()->first();
        if ($purchase) {
            $purchaseInvoice = $purchase->invoice_no;

            if ($purchaseInvoice) {
                // split the invoice number
                $split_invoice = explode('-', $purchaseInvoice);
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }
}
