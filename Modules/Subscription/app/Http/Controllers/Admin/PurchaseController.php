<?php

namespace Modules\Subscription\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Subscription\app\Models\SubscriptionHistory;
use Modules\Subscription\app\Models\SubscriptionPlan;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = SubscriptionHistory::query();
        $query->with('user');

        $query->when($request->filled('keyword'), function ($q) use ($request) {
            $q->where('plan_name', 'like', '%'.$request->keyword.'%')
                ->orWhere('plan_price', 'like', '%'.$request->keyword.'%')
                ->orWhere('expiration_date', 'like', '%'.$request->keyword.'%')
                ->orWhere('expiration', 'like', '%'.$request->keyword.'%')
                ->orWhere('payment_method', 'like', '%'.$request->keyword.'%')
                ->orWhere('transaction', 'like', '%'.$request->keyword.'%');
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->filled('user'), function ($q) use ($request) {
            $q->where('user_id', $request->user);
        });

        $orderBy = $request->filled( 'order_by' ) && $request->order_by == 1 ? 'asc' : 'desc';

        if ($request->filled('par-page')) {
            $histories = $request->get('par-page') == 'all' ? $query->orderBy( 'id', $orderBy )->get() : $query->orderBy( 'id', $orderBy )->paginate($request->get('par-page'))->withQueryString();
        } else {
            $histories = $query->orderBy( 'id', $orderBy )->paginate()->withQueryString();
        }

        $title = __('Transaction History');
        $users = User::select('name', 'id')->get();

        return view('subscription::admin.purchase_history', compact('histories', 'title', 'users'));
    }

    public function pending_payment(Request $request)
    {
        $query = SubscriptionHistory::query();
        $query->with('user')->where('payment_status', 'pending');

        $query->when($request->filled('keyword'), function ($q) use ($request) {
            $q->where('plan_name', 'like', '%'.$request->keyword.'%')
                ->orWhere('plan_price', 'like', '%'.$request->keyword.'%')
                ->orWhere('expiration_date', 'like', '%'.$request->keyword.'%')
                ->orWhere('expiration', 'like', '%'.$request->keyword.'%')
                ->orWhere('payment_method', 'like', '%'.$request->keyword.'%')
                ->orWhere('transaction', 'like', '%'.$request->keyword.'%');
        });

        $query->when($request->filled('user'), function ($q) use ($request) {
            $q->where('user_id', $request->user);
        });

        $orderBy = $request->filled( 'order_by' ) && $request->order_by == 1 ? 'asc' : 'desc';

        if ($request->filled('par-page')) {
            $histories = $request->get('par-page') == 'all' ? $query->orderBy( 'id', $orderBy )->get() : $query->orderBy( 'id', $orderBy )->paginate($request->get('par-page'))->withQueryString();
        } else {
            $histories = $query->orderBy( 'id', $orderBy )->paginate()->withQueryString();
        }

        $title = __('Pending Transaction');
        $users = User::select('name', 'id')->get();

        return view('subscription::admin.purchase_history', compact('histories', 'title', 'users'));
    }

    public function subscription_history()
    {
        $histories = SubscriptionHistory::with('user')->orderBy('id', 'desc')->where('status', 'active')->paginate(30);

        return view('subscription::admin.subscription_history', compact('histories'));
    }

    public function create()
    {

        $plans = SubscriptionPlan::where('status', 'active')->orderBy('serial', 'asc')->get();

        $users = User::all();

        return view('subscription::admin.assign_plan', compact('plans', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'plan_id' => 'required',
        ], [
            'user_id.required' => __('User is required'),
            'plan_id.required' => __('Plan is required'),
        ]);

        $plan = SubscriptionPlan::find($request->plan_id);

        if ($plan->expiration_date == 'monthly') {
            $expiration_date = date('Y-m-d', strtotime('30 days'));
        } elseif ($plan->expiration_date == 'yearly') {
            $expiration_date = date('Y-m-d', strtotime('365 days'));
        } elseif ($plan->expiration_date == 'lifetime') {
            $expiration_date = 'lifetime';
        }

        $this->store_pricing_plan($request->user_id, $plan, $expiration_date);

        $notification = __('Assign Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);

    }

    public function show($id)
    {
        $history = SubscriptionHistory::with('user')->where('id', $id)->first();

        return view('subscription::admin.purchase_history_show', compact('history'));
    }

    public function approved_plan_payment($id)
    {

        $history = SubscriptionHistory::where('id', $id)->first();

        SubscriptionHistory::where('user_id', $history->user_id)->update(['status' => 'expired']);

        $history->payment_status = 'success';
        $history->status = 'active';
        $history->save();

        $notification = __('Approved Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function plan_renew($id)
    {
        $current_plan = SubscriptionHistory::where('id', $id)->first();
        if ($current_plan->expiration != 'lifetime') {

            if ($current_plan->expiration_date <= date('Y-m-d')) {

                $plan = SubscriptionPlan::find($current_plan->subscription_plan_id);

                if ($plan->expiration_date == 'monthly') {
                    $expiration_date = date('Y-m-d', strtotime('30 days'));
                } elseif ($plan->expiration_date == 'yearly') {
                    $expiration_date = date('Y-m-d', strtotime('365 days'));
                } elseif ($plan->expiration_date == 'lifetime') {
                    $expiration_date = 'lifetime';
                }

                $this->store_pricing_plan($current_plan->user_id, $plan, $expiration_date);

            } else {
                $plan = SubscriptionPlan::find($current_plan->subscription_plan_id);

                if ($plan->expiration_date == 'monthly') {
                    $expiration_date = date('Y-m-d', strtotime($current_plan->expiration_date.' +30 days'));
                } elseif ($plan->expiration_date == 'yearly') {
                    $expiration_date = date('Y-m-d', strtotime($current_plan->expiration_date.' +365 days'));
                } elseif ($plan->expiration_date == 'lifetime') {
                    $expiration_date = 'lifetime';
                }

                $this->store_pricing_plan($current_plan->user_id, $plan, $expiration_date);
            }
        }

        $notification = __('Subscription renew successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function delete_plan_payment($id)
    {
        $history = SubscriptionHistory::where('id', $id)->first();

        if ($history->status == 'active') {
            $active_latest = SubscriptionHistory::where('user_id', $history->user_id)->where('id', '!=', $id)->orderBy('id', 'desc')->first();

            if ($active_latest) {
                $active_latest->status = 'active';
                $active_latest->save();
            }
        }
        $history->delete();

        $notification = __('Delete Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->route('admin.plan-transaction-history')->with($notification);

    }

    public function store_pricing_plan($user_id, $plan, $expiration_date)
    {

        SubscriptionHistory::where('user_id', $user_id)->update(['status' => 'expired']);

        $purchase = new SubscriptionHistory();
        $purchase->user_id = $user_id;
        $purchase->subscription_plan_id = $plan->id;
        $purchase->plan_name = $plan->plan_name;
        $purchase->plan_price = $plan->plan_price;
        $purchase->expiration = $plan->expiration_date;
        $purchase->expiration_date = $expiration_date;
        $purchase->status = 'active';
        $purchase->payment_method = 'handcash';
        $purchase->payment_status = 'success';
        $purchase->transaction = 'hand_cash';
        $purchase->save();

    }
}
