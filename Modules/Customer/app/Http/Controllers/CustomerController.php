<?php

namespace Modules\Customer\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Services\MailSenderService;
use App\Traits\GetGlobalInformationTrait;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Customer\app\Http\Services\UserGroupService;
use Modules\Customer\app\Jobs\SendBulkEmailToUser;
use Modules\Customer\app\Jobs\SendUserBannedMailJob;
use Modules\Customer\app\Models\BannedHistory;
use Modules\Customer\app\Models\CustomerDue;
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
}
