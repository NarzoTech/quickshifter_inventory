<?php

namespace Modules\Customer\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MailSenderService;
use App\Traits\GetGlobalInformationTrait;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Customer\app\Jobs\SendBulkEmailToUser;
use Modules\Customer\app\Jobs\SendUserBannedMailJob;
use Modules\Customer\app\Models\BannedHistory;

class CustomerController extends Controller
{
    use RedirectHelperTrait;

    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.view');

        $query = User::query();

        $query->when($request->filled('keyword'), function ($q) use ($request) {
            $q->where('name', 'like', '%'.$request->keyword.'%')
                ->orWhere('email', 'like', '%'.$request->keyword.'%')
                ->orWhere('phone', 'like', '%'.$request->keyword.'%')
                ->orWhere('address', 'like', '%'.$request->keyword.'%');
        });

        $orderBy = $request->filled( 'order_by' ) && $request->order_by == 1 ? 'asc' : 'desc';

        if ($request->filled('par-page')) {
            $users = $request->get('par-page') == 'all' ? $query->orderBy( 'id', $orderBy )->get() : $query->orderBy( 'id', $orderBy )->paginate($request->get('par-page'))->withQueryString();
        } else {
            $users = $query->orderBy( 'id', $orderBy )->paginate()->withQueryString();
        }

        return view('customer::customer')->with([
            'users' => $users,
        ]);
    }

    // store
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.create');

        $request->validate([
            'name' => 'required',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->city = $request->city;
        $user->tax_number = $request->tax_number;
        $user->status = $request->status;
        $user->address = $request->address;
        $user->save();

        return $this->redirectWithMessage(RedirectType::CREATE->value,'admin.customers.index',[],['messege'=>'Customer created successfully.','alert-type'=>'success']);
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
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->city = $request->city;
        $user->tax_number = $request->tax_number;
        $user->status = $request->status;
        $user->address = $request->address;
        $user->save();

        return $this->redirectWithMessage(RedirectType::UPDATE->value,'admin.customers.index',[],['messege'=>'Customer updated successfully.','alert-type'=>'success']);
    }
}
