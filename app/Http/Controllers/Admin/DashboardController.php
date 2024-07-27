<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Language\app\Models\Language;
use Modules\Product\app\Models\Product;
use Modules\Sales\app\Models\Sale;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $data['customerDues'] = CustomerDue::where('status', 1)->sum('due_amount');
        $data['todaySales'] = Sale::whereDate('order_date', date('Y-m-d'))->sum('grand_total');
        $data['totalProducts'] = Product::count();
        $data['suppliersDues'] = 0;
        return view('admin.dashboard', compact('data'));
    }

    public function setLanguage()
    {
        $lang = Language::whereCode(request('code'))->first();

        if (session()->has('lang')) {
            session()->forget('lang');
            session()->forget('text_direction');
        }
        if ($lang) {
            session()->put('lang', $lang->code);
            session()->put('text_direction', $lang->direction);

            $notification = __('Language Changed Successfully');
            $notification = ['messege' => $notification, 'alert-type' => 'success'];

            return redirect()->back()->with($notification);
        }

        session()->put('lang', config('app.locale'));

        $notification = __('Language Changed Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }
}
