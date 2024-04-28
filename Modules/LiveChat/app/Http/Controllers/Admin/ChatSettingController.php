<?php

namespace Modules\LiveChat\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\GlobalSetting\app\Http\Controllers\GlobalSettingController;
use Modules\GlobalSetting\app\Models\Setting;

class ChatSettingController extends Controller
{
    public function index()
    {
        return view('livechat::admin.chat_setting');
    }

    public function update(Request $request)
    {
        $request->validate([
            'pusher_app_id' => 'required',
            'pusher_app_key' => 'required',
            'pusher_app_secret' => 'required',
            'pusher_app_cluster' => 'required',
            'pusher_status' => 'required',

        ], [
            'pusher_app_id.required' => __('App id is required'),
            'pusher_app_key.required' => __('App key is required'),
            'pusher_app_secret.required' => __('App secret is required'),
            'pusher_app_cluster.required' => __('Cluster is required'),
            'pusher_status.required' => __('Status is required'),

        ]);

        Setting::where('key', 'pusher_app_id')->update(['value' => $request->pusher_app_id]);
        Setting::where('key', 'pusher_app_key')->update(['value' => $request->pusher_app_key]);
        Setting::where('key', 'pusher_app_secret')->update(['value' => $request->pusher_app_secret]);
        Setting::where('key', 'pusher_app_cluster')->update(['value' => $request->pusher_app_cluster]);
        Setting::where('key', 'pusher_status')->update(['value' => $request->pusher_status]);

        $setting_config = new GlobalSettingController();
        $setting_config->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);

    }
}
