<?php

namespace Modules\Customer\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Imports\CustomersImport;
use App\Models\Ledger;
use App\Models\Payment;
use App\Models\User;
use App\Services\MailSenderService;
use App\Traits\GetGlobalInformationTrait;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
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
        // checkAdminHasPermissionAndThrowException('customer.view');

        $query = User::query();

        $query->with('sales', 'payment', 'saleReturn');

        $query->when($request->filled('keyword'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->keyword . '%')
                ->orWhere('email', 'like', '%' . $request->keyword . '%')
                ->orWhere('phone', 'like', '%' . $request->keyword . '%')
                ->orWhere('address', 'like', '%' . $request->keyword . '%');
        });

        if (request('export')) {
            $fileName = 'customers-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
            return Excel::download(new CustomerExport($query->get()), $fileName);
        }
        $orderBy = $request->filled('order_by') ? $request->order_by : 'asc';

        if ($orderBy) {
            $users = $query->orderBy('name', $orderBy);
        }
        $customers = null;
        if (request()->order_type) {
            $orderBy = request()->order_by;
            $orderBy = $orderBy == 'asc' ? 'sortBy' : 'sortByDesc';
            switch (request()->order_type) {
                case 'due':
                    $customers = $query->with(['sales', 'payment', 'saleReturn'])
                        ->get()
                        ->$orderBy(function ($customer) {
                            $totalPurchase = $customer->sales->sum('grand_total');
                            $totalPaid = $customer->payment->sum('amount');
                            $totalReturn = $customer->saleReturn->sum('return_amount');
                            $totalDue = $totalPurchase - $totalPaid - $totalReturn;
                            return $totalDue;
                        });
                    break;

                case 'paid':
                    $customers = $query->with(['payment'])
                        ->get()
                        ->$orderBy(function ($customer) {
                            return $customer->payment->sum('amount');
                        });
                    break;

                case 'total':
                    $customers = $query->with(['sales'])
                        ->get()
                        ->$orderBy(function ($customer) {
                            return $customer->sales->sum('grand_total');
                        });
                    break;

                default:
                    // Default sorting logic
                    break;
            }
        }


        $data['totalSale'] = 0;
        $data['pay'] = 0;
        $data['total_return'] = 0;
        $data['total_return_pay'] = 0;
        $data['total_return_due'] = 0;
        $data['total_due'] = 0;
        $data['total_advance'] = 0;
        $data['total_due_dismiss'] = 0;

        foreach ($query->get() as $index => $customer) {
            $data['totalSale'] += $customer->sales->sum('grand_total');
            $data['pay'] += $customer->total_paid;

            $totalReturn = $customer->saleReturn->sum('return_amount');
            $data['total_return'] += $totalReturn;

            $data['total_due'] += $customer->total_due - $totalReturn;
            $data['total_return_pay'] += $totalReturn - $customer->saleReturn->sum('return_due');
            $data['total_return_due'] += $customer->saleReturn->sum('return_due');


            $data['total_advance'] += $customer->advances();
            // $data['total_due_dismiss'] += $customer->total_due_dismiss;
        }

        if (request('par-page')) {
            if (request('par-page') == 'all') {
                $perPage = $customers->count();
            } else {

                $perPage = request('par-page');
            }
        } else {
            $perPage = 20;
        }

        if (request()->order_type) {
            // Convert sorted collection to paginate manually
            $page = request('page', 1); // Default to page 1
            $paginatedCustomers = $customers->slice(($page - 1) * $perPage, $perPage)->values();
        }


        if (request('par-page')) {
            if (request('par-page') == 'all') {
                $users = request()->order_type ? $paginatedCustomers : $query->paginate();
            } else {
                $users = request()->order_type ? $paginatedCustomers : $query->paginate(request('par-page'));
            }
        } else {
            $users = request()->order_type ? $paginatedCustomers : $query->paginate(20);
        }


        if (request()->order_type) {
            $users = new LengthAwarePaginator(
                $paginatedCustomers,
                $customers->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        $users->appends(request()->query());

        $groups = $this->userGroup->getUserGroup()->where('type', 'customer')->where('status', 1)->get();
        $areaList = $this->areaService->getArea()->get();
        $vehicles = $this->vehicle->get();
        return view('customer::customer')->with([
            'users' => $users,
            'groups' => $groups,
            'areaList' => $areaList,
            'vehicles' => $vehicles,
            'data' => $data
        ]);
    }

    // store
    public function store(Request $request)
    {
        // checkAdminHasPermissionAndThrowException('customer.create');

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
        // checkAdminHasPermissionAndThrowException('customer.view');

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
        $user->plate_number = $request->plate_number;
        $user->date = now()->parse($request->date);
        $user->status = $request->status;
        $user->guest = $request->guest ? 1 : 0;
        $user->address = $request->address;
        $user->wallet_balance = $request->due;
        $user->save();

        return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.customers.index', [], ['messege' => 'Customer updated successfully.', 'alert-type' => 'success']);
    }

    public function destroy($id)
    {

        $user = User::findOrFail($id);

        // $user->due()->delete();
        // $user->payment()->delete();
        // $user->sales()->details()->delete();
        // $user->sales()->stock()->delete();
        // $user->sales()->delete();
        $user->delete();
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.customers.index', [], ['messege' => 'Customer deleted successfully.', 'alert-type' => 'success']);
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
        $user->plate_number = $request->plate_number;
        $user->wallet_balance = $request->due;
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

        DB::beginTransaction();
        try {

            // create ledger

            $ledger = new Ledger();
            $ledger->customer_id = $request->customer_id;
            $ledger->amount = $request->receiving_amount;
            $ledger->invoice_type = 'Due Receive';
            $ledger->is_paid = 0;
            $ledger->is_received = 1;
            $ledger->invoice_no = $this->genLedgerInvoiceNumber('Due Receive');
            $ledger->due_amount -= $request->receiving_amount;

            $ledger->note = $request->note;
            $ledger->date = now()->parse($request->payment_date);

            $ledger->created_by = auth('admin')->user()->id;
            $ledger->save();

            $ledger->invoice_url = route('admin.customers.ledger-details', $ledger->id);
            $ledger->save();

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
                CustomerPayment::create([
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

                // create ledger details
                $ledger->details()->create([
                    'invoice' => $invo,
                    'amount' => $request->amount[$index],
                ]);
            }



            DB::commit();
            return to_route('admin.customers.index')->with([
                'messege' => 'Customer due receive successfully.',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => $exception->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function dueReceiveList()
    {
        $payments  = CustomerPayment::whereNotNull('sale_id')->where('payment_type', 'due_receive')->where('amount', '>', 0);

        if (request('customer')) {
            $payments = $payments->where('customer_id', request('customer'));
        }

        $payments = $payments->paginate(20);

        $payments->appends(request()->query());

        return view('customer::due-list', compact('payments'));
    }

    public function dueReceiveEdit($id)
    {
        $payment = CustomerPayment::findOrFail($id);
        $accounts = $this->account->all()->get();
        return view('customer::due-receive-edit', compact('payment', 'accounts'));
    }

    public function dueReceiveUpdate(Request $request, $id)
    {
        $payment = CustomerPayment::findOrFail($id);
        $payment->update($request->except('_token'));
        return to_route('admin.customer.due-receive.list')->with([
            'messege' => 'Customer due receive successfully.',
            'alert-type' => 'success'
        ]);
    }

    public function dueReceiveDelete($id)
    {
        $payment = CustomerPayment::findOrFail($id);

        // update customer due amount
        $due = CustomerDue::where('invoice', $payment->sale->invoice)->first();
        $due->due_amount = $due->due_amount + $payment->amount;
        $due->paid_amount = $due->paid_amount - $payment->amount;
        $due->save();


        // update sale
        $sale = $payment->sale;
        $sale->payment_status = $sale->due_amount == $payment->amount ? 'paid' : 'due';
        $sale->paid_amount = $sale->paid_amount - $payment->amount;
        $sale->due_amount = $sale->due_amount + $payment->amount;
        $sale->save();


        // customer ledger delete

        $payment->delete();
        return to_route('admin.customer.due-receive.list')->with([
            'messege' => 'Customer due receive successfully.',
            'alert-type' => 'success'
        ]);
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
        $ledger->invoice_no = $this->genLedgerInvoiceNumber($ledger->invoice_type);
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


    public function genLedgerInvoiceNumber($type = 'Sale Payment')
    {
        $number = 001;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $purchase = Ledger::where('invoice_type', $type)->latest()->first();
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


    public function ledger($id)
    {
        $user = User::findOrFail($id);
        $ledgers = Ledger::where('customer_id', $user->id)->orderBy('date', 'asc')->paginate(20);
        $title = __('Customer Ledger');
        return view('supplier::ledger', compact('ledgers', 'title'));
    }

    public function ledgerDetails($id)
    {
        $ledger = Ledger::with('details', 'customer')->find($id);
        $title = __('Customer Ledger Details');
        return view('supplier::ledger-details', compact('ledger', 'title'));
    }

    public function bulkImport()
    {
        return view('customer::import');
    }
    public function bulkImportStore(Request $request)
    {
        $request->validate(['file' => 'required']);
        try {
            $file = $request->file('file');
            Excel::import(new CustomersImport, $file);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.customers.index', [], ['messege' => 'Supplier imported successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.customers.index', [], ['messege' => 'Supplier imported failed.', 'alert-type' => 'error']);
        }
    }


    public function deleteAllCustomer(Request $request)
    {

        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, auth('admin')->user()->password)) {
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => 'Password does not match.', 'alert-type' => 'error']);
        }

        $users = User::all();

        // delete ledger
        foreach ($users as $user) {
            if ($user->due()?->exists()) {
                $user->due()?->delete();
            }
            if ($user->payment()->exists()) {
                $user->payment()?->delete();
            }
            if ($user->sales()->exists()) {

                foreach ($user->sales as $sale) {
                    $sale->details()?->delete();
                    $sale->stock()?->delete();
                    $sale->delete();
                }
            }
            if ($user->orderReviews()->exists()) {
                $user->orderReviews()?->delete();
            }

            $user->delete();
        }


        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.customers.index', [], ['messege' => 'Customer deleted successfully.', 'alert-type' => 'success']);
    }
}
