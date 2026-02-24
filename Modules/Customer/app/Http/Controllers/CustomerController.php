<?php
namespace Modules\Customer\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Exports\CustomerExport;
use App\Exports\LedgerExport;
use App\Http\Controllers\Controller;
use App\Imports\CustomersImport;
use App\Models\Ledger;
use App\Models\LedgerDetails;
use App\Models\User;
use App\Traits\RedirectHelperTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\app\Models\Account;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Customer\app\Http\Services\UserGroupService;
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

        $query->with('sales', 'payment', 'saleReturn');

        $query->when($request->filled('keyword'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->keyword . '%')
                ->orWhere('email', 'like', '%' . $request->keyword . '%')
                ->orWhere('phone', 'like', '%' . $request->keyword . '%')
                ->orWhere('address', 'like', '%' . $request->keyword . '%');
        });

        // Date filtering
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
            $query->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($request->filled('from_date')) {
            $fromDate = Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
            $query->where('date', '>=', $fromDate);
        } elseif ($request->filled('to_date')) {
            $toDate = Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
            $query->where('date', '<=', $toDate);
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
                        ->get();
                    $customers = $customers->filter(function ($customer) {
                        return $customer->getTotalDueAttribute() > 0;
                    });
                    $customers = $customers->$orderBy(function ($customer) {
                        $totalPurchase = $customer->sales->sum('grand_total');
                        $totalPaid     = $customer->payment->sum('amount');
                        $totalReturn   = $customer->saleReturn->sum('return_amount');
                        $totalDue      = $totalPurchase - $totalPaid - $totalReturn;
                        return $totalDue;
                    });
                    break;

                case 'paid':
                    $customers = $query->with(['payment', 'sales'])
                        ->whereHas('sales')
                        ->get();
                    $customers = $customers->filter(function ($customer) {
                        return $customer->sales->sum('grand_total') == $customer->getTotalPaidAttribute();
                    });
                    $customers = $customers->$orderBy(function ($customer) {
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

        $data['totalSale']         = 0;
        $data['pay']               = 0;
        $data['total_return']      = 0;
        $data['total_return_pay']  = 0;
        $data['total_return_due']  = 0;
        $data['total_due']         = 0;
        $data['sale_due']          = 0;
        $data['previous_due']      = 0;
        $data['total_advance']     = 0;
        $data['total_due_dismiss'] = 0;

        $customerData = request()->order_type ? $customers : $query->get();
        foreach ($customerData as $index => $customer) {
            $data['totalSale'] += $customer->sales->sum('grand_total');

            $totalReturn           = $customer->saleReturn->sum('return_amount');
            $data['total_return'] += $totalReturn;

            $data['total_return_pay'] += $totalReturn - $customer->saleReturn->sum('return_due');
            $data['total_return_due'] += $customer->saleReturn->sum('return_due');

            $rawDue = $customer->total_due - $totalReturn;
            $rawAdvance = $customer->advances();
            $offset = min(max(0, $rawDue), max(0, $rawAdvance));
            $data['pay']           += $customer->total_paid + $offset;
            $data['total_due']     += $rawDue - $offset;
            $data['sale_due']      += $customer->sale_due;
            $data['previous_due']  += $customer->previous_due;
            $data['total_advance'] += $rawAdvance - $offset;
            // $data['total_due_dismiss'] += $customer->total_due_dismiss;
        }

        if (request('par-page')) {
            if (request('par-page') == 'all') {

                $perPage = request()->order_type ? $customers->count() : $customerData->count();
            } else {
                $perPage = request('par-page');
            }
        } else {
            $perPage = 20;
        }

        if (request()->order_type) {
                                                      // Convert sorted collection to paginate manually
            $page               = request('page', 1); // Default to page 1
            $paginatedCustomers = $customerData->slice(($page - 1) * $perPage, $perPage)->values();
        }

        if (checkAdminHasPermission('customer.excel.download')) {
            if (request('export')) {
                $fileName = 'customers-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new CustomerExport($customerData, $data), $fileName);
            }
        }
        if (checkAdminHasPermission('customer.pdf.download')) {
            if (request('export_pdf')) {
                $html = view('customer::pdf.customer', [
                    'users' => $customerData,
                    'data'  => $data,
                ])->render();

                $pdf = $fileName = 'customer-list-' . date('Y-m-d') . '_' . date('h-i-s') . '.pdf';
                $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape')->setOption('enable_javascript')->setOption('isTableHeaderRepeat', false)->setOption('isRemoteEnabled', true)->setWarnings(false);
                return $pdf->download($fileName);
            }
        }

        if (request()->order_type) {
            $users = new LengthAwarePaginator(
                $paginatedCustomers,
                $customerData->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $users = $query->paginate($perPage);
        }
        $users = $users->appends(request()->query());

        $groups   = $this->userGroup->getUserGroup('dropdown')->where('type', 'customer')->where('status', 1)->get();
        $areaList = $this->areaService->getArea()->get();
        $vehicles = $this->vehicle->get();
        return view('customer::customer')->with([
            'users'    => $users,
            'groups'   => $groups,
            'areaList' => $areaList,
            'vehicles' => $vehicles,
            'data'     => $data,
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
            $view      = view('pos::customer-drop-down', compact('customers'))->render();
            return response()->json([
                'message'    => 'Customer created successfully.',
                'alert-type' => 'success',
                'view'       => $view,
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
            'user'             => $user,
            'banned_histories' => $banned_histories,
        ]);
    }

    // update

    public function update(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('customer.edit');

        $request->validate([
            'name'  => 'required',
            'phone' => 'nullable|unique:users,phone,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $id,
        ]);

        $user                 = User::findOrFail($id);
        $user->name           = $request->name;
        $user->phone          = $request->phone;
        $user->email          = $request->email;
        $user->group_id       = $request->group_id;
        $user->area_id        = $request->area_id;
        $user->vehicle_id     = $request->vehicle_id;
        $user->membership     = $request->membership;
        $user->plate_number   = $request->plate_number;
        $user->date           = $request->date ? Carbon::createFromFormat('d-m-Y', $request->date) : now();
        $user->status         = $request->status;
        $user->guest          = $request->guest ? 1 : 0;
        $user->address        = $request->address;
        $user->wallet_balance = $request->due;
        $user->save();

        return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.customers.index', [], ['messege' => 'Customer updated successfully.', 'alert-type' => 'success']);
    }

    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('customer.delete');
        $user = User::findOrFail($id);

        $user->due()->delete();
        $user->payment()->delete();

        // Delete related records for each sale
        foreach ($user->sales as $sale) {
            $sale->details()->delete();
            $sale->stock()->delete();
        }
        $user->sales()->delete();
        $user->delete();

        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.customers.index', [], ['messege' => 'Customer deleted successfully.', 'alert-type' => 'success']);
    }

    public function saveUser(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'phone' => 'nullable|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $user                 = new User();
        $user->name           = $request->name;
        $user->phone          = $request->phone;
        $user->email          = $request->email;
        $user->group_id       = $request->group_id;
        $user->area_id        = $request->area_id;
        $user->vehicle_id     = $request->vehicle_id;
        $user->membership     = $request->membership;
        $user->plate_number   = $request->plate_number;
        $user->wallet_balance = $request->due;
        $user->date           = $request->date ? Carbon::createFromFormat('d-m-Y', $request->date) : now();
        $user->status         = $request->status;
        $user->guest          = $request->guest ? 1 : 0;
        $user->address        = $request->address;
        $user->save();

        return $user;
    }

    public function singleCustomer($id)
    {
        $user = User::findOrFail($id);
        $data = $user->toArray();
        $data['advance_balance'] = $user->advances();
        return response()->json($data);
    }

    public function dueReceiveForm(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.due.receive');
        if (! $request->customer) {
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => 'Customer Not Found', 'alert-type' => 'error']);
        }

        $customer = User::where('id', $request->customer)->first();

        if (!$customer) {
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => 'Customer Not Found', 'alert-type' => 'error']);
        }

        // Check if customer has any dues (invoice-based OR direct balance)
        $hasInvoiceDues = !$customer->due->isEmpty();
        $hasDirectBalance = ($customer->wallet_balance ?? 0) > 0;

        if (!$hasInvoiceDues && !$hasDirectBalance) {
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.customers.index', [], [
                'messege' => 'This customer has no due amount to receive.',
                'alert-type' => 'error'
            ]);
        }

        $accounts = $this->account->all()->get();

        return view('customer::due-receive', compact('customer', 'accounts', 'hasInvoiceDues', 'hasDirectBalance'));
    }

    public function dueReceive(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.due.receive');

        // Base validation
        $request->validate([
            'receiving_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required',
            'account_id' => 'required',
        ]);

        $hasInvoicePayment = $request->has('invoice_no') && is_array($request->invoice_no) && count($request->invoice_no) > 0;
        $directBalanceAmount = (float) ($request->direct_balance_amount ?? 0);

        // Validate that at least one type of payment is being made
        $totalInvoiceAmount = 0;
        if ($hasInvoicePayment) {
            foreach ($request->amount as $amt) {
                $totalInvoiceAmount += (float) ($amt ?? 0);
            }
        }

        if ($totalInvoiceAmount <= 0 && $directBalanceAmount <= 0) {
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], [
                'messege' => 'Please enter an amount for at least one invoice or direct balance.',
                'alert-type' => 'error'
            ]);
        }

        DB::beginTransaction();
        try {
            // Get account
            $account = $request->account_id;
            if ($account == 'cash' || $account == 'advance') {
                $account = $this->account->all()->where('account_type', $account)->first();
            } else {
                $account = $this->account->all()->find($account);
            }

            // Create main ledger entry
            $ledger                = new Ledger();
            $ledger->customer_id   = $request->customer_id;
            $ledger->amount        = $request->receiving_amount;
            $ledger->invoice_type  = 'Due Receive';
            $ledger->is_paid       = 0;
            $ledger->is_received   = 1;
            $ledger->invoice_no    = $this->genLedgerInvoiceNumber('Due Receive');
            $ledger->due_amount   -= $request->receiving_amount;
            $ledger->note = $request->note;
            $ledger->date = Carbon::createFromFormat('d-m-Y', $request->payment_date);
            $ledger->created_by = auth('admin')->user()->id;
            $ledger->save();

            $ledger->invoice_url = route('admin.customers.ledger-details', $ledger->id);
            $ledger->save();

            // Process invoice-based dues
            if ($hasInvoicePayment) {
                foreach ($request->invoice_no as $index => $invo) {
                    $paymentAmount = (float) ($request->amount[$index] ?? 0);

                    if ($paymentAmount <= 0) {
                        continue;
                    }

                    $sale = Sale::where('invoice', $invo)->first();

                    if ($sale) {
                        $sale->payment_status = $sale->due_amount == $paymentAmount ? 'paid' : 'due';
                        $sale->paid_amount = $sale->paid_amount + $paymentAmount;
                        $sale->due_amount  = $sale->due_amount - $paymentAmount;
                        $sale->save();

                        // Create payment data
                        CustomerPayment::create([
                            'sale_id'      => $sale->id,
                            'customer_id'  => $request->customer_id,
                            'account_id'   => $account->id,
                            'payment_type' => 'due_receive',
                            'is_received'  => 1,
                            'amount'       => $paymentAmount,
                            'payment_date' => Carbon::createFromFormat('d-m-Y', $request->payment_date),
                            'note'         => $request->note,
                            'created_by'   => auth('admin')->user()->id,
                        ]);

                        // Update customer due record
                        $due = CustomerDue::where('invoice', $invo)->first();
                        if ($due) {
                            $due->due_amount  = $due->due_amount - $paymentAmount;
                            $due->paid_amount = $due->paid_amount + $paymentAmount;
                            $due->save();
                        }

                        // Create ledger details
                        $ledger->details()->create([
                            'invoice' => $invo,
                            'amount'  => $paymentAmount,
                        ]);
                    }
                }
            }

            // Process direct balance due
            if ($directBalanceAmount > 0) {
                $customer = User::find($request->customer_id);

                if ($customer) {
                    // Update customer wallet balance
                    $customer->wallet_balance = ($customer->wallet_balance ?? 0) - $directBalanceAmount;
                    $customer->save();

                    // Create payment record for direct balance (no sale_id)
                    CustomerPayment::create([
                        'sale_id'      => null,
                        'customer_id'  => $request->customer_id,
                        'account_id'   => $account->id,
                        'payment_type' => 'direct_due_receive',
                        'is_received'  => 1,
                        'amount'       => $directBalanceAmount,
                        'payment_date' => Carbon::createFromFormat('d-m-Y', $request->payment_date),
                        'note'         => $request->note ?? 'Direct balance due receive',
                        'created_by'   => auth('admin')->user()->id,
                    ]);

                    // Create ledger details for direct balance
                    $ledger->details()->create([
                        'invoice' => 'DIRECT-BALANCE',
                        'amount'  => $directBalanceAmount,
                    ]);
                }
            }

            DB::commit();
            return to_route('admin.customers.index')->with([
                'messege'    => 'Customer due receive successfully.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => $exception->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function dueReceiveList()
    {
        checkAdminHasPermissionAndThrowException('customer.due.receive.list');
        // Include both invoice-based and direct balance due receives
        $payments = CustomerPayment::whereIn('payment_type', ['due_receive', 'direct_due_receive'])->where('amount', '>', 0);
        // Date filtering
        if (request()->from_date && request()->to_date) {
            $fromDate = \Carbon\Carbon::parse(request()->from_date)->startOfDay();
            $toDate   = \Carbon\Carbon::parse(request()->to_date)->endOfDay();
            $payments = $payments->whereBetween('payment_date', [$fromDate, $toDate]);
        }

        // Keyword search
        if (request()->keyword) {
            $keyword  = '%' . request()->keyword . '%';
            $payments = $payments->where(function ($q) use ($keyword) {
                $q->where('note', 'like', $keyword)
                    ->orWhere('amount', 'like', $keyword)

                    ->orWhereHas('customer', function ($query) use ($keyword) {
                        $query->where('name', 'like', $keyword)
                            ->orWhere('phone', 'like', $keyword)
                            ->orWhere('address', 'like', $keyword)
                            ->orWhere('email', 'like', $keyword);
                    });
            })
                ->orWhere('invoice', 'like', $keyword)
                ->orWhere('account_type', 'like', $keyword);
        }

        if (request()->order_by) {
            $payments = $payments->orderBy('payment_date', request()->order_by);
        } else {
            $payments = $payments->orderBy('payment_date', 'desc');
        }

        if (request('customer')) {
            $payments = $payments->where('customer_id', request('customer'));
        }
        $data['total'] = $payments->sum('amount');
        $payments      = $payments->paginate(20);

        $payments->appends(request()->query());

        return view('customer::due-list', compact('payments', 'data'));
    }

    public function dueReceiveEdit($id)
    {
        checkAdminHasPermissionAndThrowException('customer.due.receive.edit');
        $payment  = CustomerPayment::findOrFail($id);
        $accounts = $this->account->all()->get();
        return view('customer::due-receive-edit', compact('payment', 'accounts'));
    }

    public function dueReceiveUpdate(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('customer.due.receive.edit');
        $payment = CustomerPayment::findOrFail($id);
        $payment->update($request->except('_token'));
        return to_route('admin.customer.due-receive.list')->with([
            'messege'    => 'Customer due receive successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function dueReceiveDelete($id)
    {
        checkAdminHasPermissionAndThrowException('customer.due.receive.delete');
        $payment = CustomerPayment::findOrFail($id);

        // Check if this is a direct balance payment or invoice-based payment
        if ($payment->payment_type == 'direct_due_receive' || $payment->sale_id == null) {
            // Direct balance payment - restore customer wallet balance
            $customer = User::find($payment->customer_id);
            if ($customer) {
                $customer->wallet_balance = ($customer->wallet_balance ?? 0) + $payment->amount;
                $customer->save();
            }

            // Delete ledger detail for direct balance
            $ledgerDetail = LedgerDetails::where('invoice', 'DIRECT-BALANCE')
                ->whereHas('ledger', function($q) use ($payment) {
                    $q->where('customer_id', $payment->customer_id)
                      ->whereDate('date', $payment->payment_date);
                })
                ->first();

            if ($ledgerDetail) {
                $ledger = $ledgerDetail->ledger;
                $otherDetailsCount = LedgerDetails::where('ledger_id', $ledger->id)
                    ->where('id', '!=', $ledgerDetail->id)
                    ->count();

                $ledgerDetail->delete();

                if ($otherDetailsCount == 0) {
                    $ledger->delete();
                } else {
                    $ledger->amount = $ledger->amount - $payment->amount;
                    $ledger->due_amount = $ledger->due_amount + $payment->amount;
                    $ledger->save();
                }
            }
        } else {
            // Invoice-based payment
            // update customer due amount
            $due = CustomerDue::where('invoice', $payment->sale->invoice)->first();
            if ($due) {
                $due->due_amount  = $due->due_amount + $payment->amount;
                $due->paid_amount = $due->paid_amount - $payment->amount;
                $due->save();
            }

            // update sale
            $sale = $payment->sale;
            if ($sale) {
                $sale->payment_status = 'due';
                $sale->paid_amount    = $sale->paid_amount - $payment->amount;
                $sale->due_amount     = $sale->due_amount + $payment->amount;
                $sale->save();
            }

            // customer ledger delete
            $invoiceNumber = $payment->sale->invoice ?? null;
            if ($invoiceNumber) {
                $ledgerDetail = LedgerDetails::where('invoice', $invoiceNumber)->first();

                if ($ledgerDetail) {
                    $ledger = $ledgerDetail->ledger;

                    $otherDetailsCount = LedgerDetails::where('ledger_id', $ledger->id)
                        ->where('id', '!=', $ledgerDetail->id)
                        ->count();

                    $ledgerDetail->delete();

                    if ($otherDetailsCount == 0) {
                        $ledger->delete();
                    } else {
                        $ledger->amount     = $ledger->amount - $payment->amount;
                        $ledger->due_amount = $ledger->due_amount + $payment->amount;
                        $ledger->save();
                    }
                }
            }
        }

        $payment->delete();
        return to_route('admin.customer.due-receive.list')->with([
            'messege'    => 'Customer due receive deleted successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function changeStatus($id)
    {
        checkAdminHasPermissionAndThrowException('customer.status');
        $user = User::find($id);

        $status = $user->status == 1 ? 0 : 1;

        $user->status = $status;
        $user->save();

        $notification = $status == 1 ? 'Customer activated' : 'Customer deactivated';

        return response()->json(['status' => 'success', 'message' => $notification]);
    }

    public function advance($id)
    {
        checkAdminHasPermissionAndThrowException('customer.advance');
        $customer = User::find($id);
        $accounts = $this->account->all()->with('bank')->get();
        return view('customer::advance', compact('customer', 'accounts'));
    }

    public function advanceStore(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('customer.advance');
        $rules = [
            'date'          => 'required',
            'total_amount'  => 'required',
            'payment_type'  => 'required',
        ];

        if ($request->has('paying_amount')) {
            $rules['paying_amount'] = 'required|numeric|min:0.01';
        } elseif ($request->has('refund_amount')) {
            $rules['refund_amount'] = 'required|numeric|min:0.01';
        }

        $validator = Validator::make($request->all(), $rules);

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
        checkAdminHasPermissionAndThrowException('customer.advance');
        $account = $request->account_id;

        if ($account == 'cash' || $account == 'advance') {
            $account = Account::where('account_type', $account)?->first();
        } else {
            $account = Account::find($account);
        }

        // create payment data
        // advance_receive: is_paid=0, is_received=1 (receiving money from customer)
        // advance_refund: is_paid=1, is_received=0 (paying money back to customer)
        CustomerPayment::create([
            'customer_id'  => $id,
            'account_id'   => $account->id,
            'payment_type' => $request->refund_amount != null ? 'advance_refund' : 'advance_receive',
            'is_paid'      => $request->refund_amount != null ? 1 : 0,
            'is_received'  => $request->refund_amount != null ? 0 : 1,
            'amount'       => $request->refund_amount != null ? $request->refund_amount : $request->paying_amount,
            'account_type' => accountList()[$account->account_type],
            'note'         => $request->note,
            'created_by'   => auth('admin')->user()->id,
            'payment_date' => Carbon::createFromFormat('d-m-Y', $request->date),
            'invoice'      => $this->genInvoiceNumber(),
        ]);

        // create ledger

        $ledger               = new Ledger();
        $ledger->customer_id  = $id;
        $ledger->amount       = $request->paying_amount ?? $request->refund_amount;
        $ledger->invoice_type = $request->refund_amount == null ? 'Advance Received' : 'Payment Return';
        $ledger->is_paid      = $request->refund_amount != null ? 1 : 0;
        $ledger->is_received  = $request->refund_amount != null ? 0 : 1;
        $ledger->invoice_no   = $this->genLedgerInvoiceNumber($ledger->invoice_type);
        $ledger->note         = $request->note;

        if ($request->refund_amount != null) {
            $ledger->due_amount += $request->refund_amount;
            $ledger->amount      = -$request->refund_amount;
        } else {
            $ledger->due_amount = -$request->paying_amount;
            $ledger->amount     = $request->paying_amount;
        }
        $ledger->date       = Carbon::createFromFormat('d-m-Y', $request->date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();

        $ledger->invoice_url = route('admin.customers.ledger-details', $ledger->id);
        $ledger->save();
    }

    public function genInvoiceNumber()
    {
        return generateInvoiceNumber(CustomerPayment::class, 'invoice', 'CP-');
    }

    public function genLedgerInvoiceNumber($type = 'Sale Payment')
    {
        $prefixMap = [
            'Due Receive'      => 'DRL-',
            'Advance Received' => 'CAL-',
            'Payment Return'   => 'CAL-',
        ];
        $prefix = $prefixMap[$type] ?? 'CL-';
        return generateInvoiceNumber(Ledger::class, 'invoice_no', $prefix, ['invoice_type' => $type]);
    }

    public function ledger($id)
    {
        checkAdminHasPermissionAndThrowException('customer.ledger');
        $user    = User::findOrFail($id);
        // Reconstruct original wallet_balance before any direct due receives,
        // since direct_due_receive both reduces wallet_balance AND creates a ledger entry
        $directDueReceived = CustomerPayment::where('customer_id', $user->id)
            ->where('payment_type', 'direct_due_receive')
            ->sum('amount');
        $walletBalance = ($user->wallet_balance ?? 0) + $directDueReceived;

        // Calculate opening balance from entries before from_date
        $balanceBeforeFromDate = $walletBalance;
        if (request('from_date')) {
            $fromDate = \Carbon\Carbon::createFromFormat('d-m-Y', request('from_date'))->startOfDay();
            $balanceBeforeFromDate += Ledger::where('customer_id', $user->id)
                ->where('date', '<', $fromDate)
                ->sum('due_amount');
        }

        $baseQuery = Ledger::where('customer_id', $user->id);

        // Apply date filtering if provided
        if (request('from_date')) {
            $fromDate = \Carbon\Carbon::createFromFormat('d-m-Y', request('from_date'))->startOfDay();
            $baseQuery->where('date', '>=', $fromDate);
        }
        if (request('to_date')) {
            $toDate = \Carbon\Carbon::createFromFormat('d-m-Y', request('to_date'))->endOfDay();
            $baseQuery->where('date', '<=', $toDate);
        }

        $baseQuery->orderBy('date', 'asc');

        // Get all filtered ledgers for total calculation
        $allLedgers = (clone $baseQuery)->get();

        // Calculate grand totals from filtered records
        $totals = [
            'credit' => $allLedgers->sum('amount'),
            'debit' => $allLedgers->sum('total_amount'),
            'balance' => $balanceBeforeFromDate + $allLedgers->sum('due_amount'),
        ];

        // Paginate for display
        $perPage = 20;
        $ledgers = $baseQuery->paginate($perPage);
        $ledgers->appends(request()->query());

        // Calculate opening balance for current page
        $currentPage = $ledgers->currentPage();
        $skipCount = ($currentPage - 1) * $perPage;
        $previousDueSum = $allLedgers->take($skipCount)->sum('due_amount');
        $openingBalance = $balanceBeforeFromDate + $previousDueSum;

        $title = __('Customer Ledger');

        if (request('export')) {
            $fileName = 'customer-ledger-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
            return Excel::download(new LedgerExport($allLedgers, $title, $balanceBeforeFromDate), $fileName);
        }
        if (request('export_pdf')) {
            return view('supplier::pdf.ledger', ['ledgers' => $allLedgers, 'title' => $title, 'openingBalance' => $balanceBeforeFromDate]);
        }
        return view('supplier::ledger', compact('ledgers', 'title', 'openingBalance', 'totals'));
    }

    public function ledgerDetails($id)
    {
        checkAdminHasPermissionAndThrowException('customer.ledger');
        $ledger = Ledger::with('details', 'customer')->find($id);
        $title  = __('Customer Ledger Details');
        return view('supplier::ledger-details', compact('ledger', 'title'));
    }

    public function bulkImport()
    {
        checkAdminHasPermissionAndThrowException('customer.bulk.import');
        return view('customer::import');
    }
    public function bulkImportStore(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.bulk.import');
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
        checkAdminHasPermissionAndThrowException('customer.bulk.delete');
        $request->validate([
            'password' => 'required',
        ]);

        if (! Hash::check($request->password, auth('admin')->user()->password)) {
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

    public function offsetDueWithAdvance(Request $request)
    {
        $request->validate(['customer_id' => 'required|exists:users,id']);

        DB::beginTransaction();
        try {
            $customer = User::findOrFail($request->customer_id);
            $advanceBalance = $customer->advances();
            $totalDue = $customer->total_due;

            if ($advanceBalance <= 0 || $totalDue <= 0) {
                return response()->json(['success' => false, 'message' => 'No advance or due to offset'], 422);
            }

            $offsetAmount = min($advanceBalance, $totalDue);

            $account = Account::where('account_type', 'advance')->first();
            if (!$account) {
                $account = Account::create(['account_type' => 'advance']);
            }

            // Create Due Receive ledger
            $ledger = new Ledger();
            $ledger->customer_id = $customer->id;
            $ledger->amount = $offsetAmount;
            $ledger->invoice_type = 'Due Receive';
            $ledger->is_paid = 0;
            $ledger->is_received = 1;
            $ledger->invoice_no = $this->genLedgerInvoiceNumber('Due Receive');
            $ledger->due_amount = -$offsetAmount;
            $ledger->note = 'Auto offset due with advance balance';
            $ledger->date = now();
            $ledger->created_by = auth('admin')->user()->id;
            $ledger->save();
            $ledger->invoice_url = route('admin.customers.ledger-details', $ledger->id);
            $ledger->save();

            // Apply to due sales (oldest first)
            $remaining = $offsetAmount;
            $dueSales = Sale::where('customer_id', $customer->id)
                ->where('due_amount', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($dueSales as $sale) {
                if ($remaining <= 0) break;

                $payAmount = min($remaining, $sale->due_amount);
                $sale->paid_amount += $payAmount;
                $sale->due_amount -= $payAmount;
                $sale->payment_status = $sale->due_amount == 0 ? 'paid' : 'due';
                $sale->save();

                CustomerPayment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $customer->id,
                    'account_id' => $account->id,
                    'payment_type' => 'due_receive',
                    'is_received' => 1,
                    'amount' => $payAmount,
                    'payment_date' => now(),
                    'note' => 'Auto offset with advance',
                    'created_by' => auth('admin')->user()->id,
                ]);

                $due = CustomerDue::where('invoice', $sale->invoice)->first();
                if ($due) {
                    $due->due_amount -= $payAmount;
                    $due->paid_amount += $payAmount;
                    $due->save();
                }

                $ledger->details()->create([
                    'invoice' => $sale->invoice,
                    'amount' => $payAmount,
                ]);

                $remaining -= $payAmount;
            }

            $actualOffset = $offsetAmount - $remaining;

            // Create Advance Deduct payment
            CustomerPayment::create([
                'customer_id' => $customer->id,
                'account_id' => $account->id,
                'payment_type' => 'advance_deduct',
                'is_received' => 1,
                'amount' => $actualOffset,
                'payment_date' => now(),
                'note' => 'Auto offset due with advance',
                'created_by' => auth('admin')->user()->id,
            ]);

            // Create Advance Deduct ledger
            $advLedger = new Ledger();
            $advLedger->customer_id = $customer->id;
            $advLedger->amount = 0;
            $advLedger->total_amount = 0;
            $advLedger->due_amount = $actualOffset;
            $advLedger->invoice_type = 'Advance Deduct';
            $advLedger->is_received = 1;
            $advLedger->invoice_no = $ledger->invoice_no;
            $advLedger->date = now();
            $advLedger->created_by = auth('admin')->user()->id;
            $advLedger->save();

            DB::commit();

            $customer->refresh();
            return response()->json([
                'success' => true,
                'message' => "Offset {$actualOffset} from advance against due successfully",
                'advance_balance' => $customer->advances(),
                'total_due' => $customer->total_due,
                'offset_amount' => $actualOffset,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
