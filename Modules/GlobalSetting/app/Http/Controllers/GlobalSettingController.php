<?php

namespace Modules\GlobalSetting\app\Http\Controllers;

use ZipArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Modules\GlobalSetting\app\Enums\AllTimeZoneEnum;
use Modules\GlobalSetting\app\Models\Setting;
use Modules\GlobalSetting\app\Models\CustomCode;
use Modules\GlobalSetting\app\Models\SeoSetting;
use Modules\GlobalSetting\app\Models\CustomPagination;

class GlobalSettingController extends Controller
{
    protected $cachedSetting;

    public function __construct()
    {
        $this->cachedSetting = Cache::get('setting');
    }

    public function general_setting()
    {
        checkAdminHasPermissionAndThrowException('setting.view');
        $custom_paginations = CustomPagination::all();
        $all_timezones = AllTimeZoneEnum::getAll();

        return view('globalsetting::settings.index', compact('custom_paginations','all_timezones'));
    }

    public function update_general_setting(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');

        $request->validate([
            'app_name' => 'required',
            'timezone' => 'required',
            'is_queable' => 'required|in:active,inactive',
        ], [
            'app_name' => __('App name is required'),
            'timezone' => __('Timezone is required'),
            'is_queable' => __('Queue is required'),
            'is_queable.in' => __('Queue is invalid'),
        ]);

        Setting::where('key', 'app_name')->update(['value' => $request->app_name]);
        Setting::where('key', 'timezone')->update(['value' => $request->timezone]);
        Setting::where('key', 'is_queable')->update(['value' => $request->is_queable]);

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function update_logo_favicon(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');

        if ($request->file('logo')) {
            $file_name = file_upload($request->logo, 'uploads/custom-images/', $this->cachedSetting?->logo);
            Setting::where('key', 'logo')->update(['value' => $file_name]);
        }

        if ($request->file('favicon')) {
            $file_name = file_upload($request->favicon, 'uploads/custom-images/', $this->cachedSetting?->favicon);
            Setting::where('key', 'favicon')->update(['value' => $file_name]);
        }

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function update_cookie_consent(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        $request->validate([
            'cookie_status' => 'required',
            'border' => 'required',
            'corners' => 'required',
            'background_color' => 'required',
            'text_color' => 'required',
            'border_color' => 'required',
            'btn_bg_color' => 'required',
            'btn_text_color' => 'required',
            'link_text' => 'required',
            'btn_text' => 'required',
            'message' => 'required',
        ], [
            'cookie_status.required' => __('Status is required'),
            'border.required' => __('Border is required'),
            'corners.required' => __('Corner is required'),
            'background_color.required' => __('Background color is required'),
            'text_color.required' => __('Text color is required'),
            'border_color.required' => __('Border Color is required'),
            'btn_bg_color.required' => __('Button color is required'),
            'btn_text_color.required' => __('Button text color is required'),
            'link_text.required' => __('Link text is required'),
            'btn_text.required' => __('Button text is required'),
            'message.required' => __('Message is required'),
        ]);

        Setting::where('key', 'cookie_status')->update(['value' => $request->cookie_status]);
        Setting::where('key', 'border')->update(['value' => $request->border]);
        Setting::where('key', 'corners')->update(['value' => $request->corners]);
        Setting::where('key', 'background_color')->update(['value' => $request->background_color]);
        Setting::where('key', 'text_color')->update(['value' => $request->text_color]);
        Setting::where('key', 'border_color')->update(['value' => $request->border_color]);
        Setting::where('key', 'btn_bg_color')->update(['value' => $request->btn_bg_color]);
        Setting::where('key', 'btn_text_color')->update(['value' => $request->btn_text_color]);
        Setting::where('key', 'link_text')->update(['value' => $request->link_text]);
        Setting::where('key', 'btn_text')->update(['value' => $request->btn_text]);
        Setting::where('key', 'message')->update(['value' => $request->message]);

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function update_custom_pagination(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        foreach ($request->quantities as $index => $quantity) {
            if ($request->quantities[$index] == '') {
                $notification = [
                    'messege' => __('Every field are required'),
                    'alert-type' => 'error',
                ];

                return redirect()->back()->with($notification);
            }

            $custom_pagination = CustomPagination::find($request->ids[$index]);
            $custom_pagination->item_qty = $request->quantities[$index];
            $custom_pagination->save();
        }

        // Cache update
        $custom_pagination = CustomPagination::all();
        $pagination = [];
        foreach ($custom_pagination as $item) {
            $pagination[str_replace(' ', '_', strtolower($item->section_name))] = $item->item_qty;
        }
        $pagination = (object) $pagination;
        Cache::put('CustomPagination', $pagination);

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);

    }

    public function update_default_avatar(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');

        if ($request->file('default_avatar')) {
            $file_name = file_upload($request->default_avatar, 'uploads/custom-images/', $this->cachedSetting?->default_avatar);
            Setting::where('key', 'default_avatar')->update(['value' => $file_name]);
        }

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);

    }

    public function update_breadcrumb(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');

        if ($request->file('breadcrumb_image')) {
            $file_name = file_upload($request->breadcrumb_image, 'uploads/custom-images/', $this->cachedSetting?->breadcrumb_image);
            Setting::where('key', 'breadcrumb_image')->update(['value' => $file_name]);
        }

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function update_copyright_text(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        $request->validate([
            'copyright_text' => 'required',
        ], [
            'copyright_text' => __('Copyright Text is required'),
        ]);
        Setting::where('key', 'copyright_text')->update(['value' => $request->copyright_text]);

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function crediential_setting()
    {
        checkAdminHasPermissionAndThrowException('setting.view');

        return view('globalsetting::credientials.index');
    }

    public function update_google_captcha(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        $request->validate([
            'recaptcha_site_key' => 'required',
            'recaptcha_secret_key' => 'required',
            'recaptcha_status' => 'required',
        ], [
            'recaptcha_site_key.required' => __('Site key is required'),
            'recaptcha_secret_key.required' => __('Secret key is required'),
            'recaptcha_status.required' => __('Status is required'),
        ]);

        Setting::where('key', 'recaptcha_site_key')->update(['value' => $request->recaptcha_site_key]);
        Setting::where('key', 'recaptcha_secret_key')->update(['value' => $request->recaptcha_secret_key]);
        Setting::where('key', 'recaptcha_status')->update(['value' => $request->recaptcha_status]);

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function update_tawk_chat(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        $request->validate([
            'tawk_status' => 'required',
            'tawk_chat_link' => 'required',
        ], [
            'tawk_status.required' => __('Status is required'),
            'tawk_chat_link.required' => __('Chat link is required'),
        ]);

        Setting::where('key', 'tawk_status')->update(['value' => $request->tawk_status]);
        Setting::where('key', 'tawk_chat_link')->update(['value' => $request->tawk_chat_link]);

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function update_google_analytic(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        $request->validate([
            'google_analytic_status' => 'required',
            'google_analytic_id' => 'required',
        ], [
            'google_analytic_status.required' => __('Status is required'),
            'google_analytic_id.required' => __('Analytic id is required'),
        ]);

        Setting::where('key', 'google_analytic_status')->update(['value' => $request->google_analytic_status]);
        Setting::where('key', 'google_analytic_id')->update(['value' => $request->google_analytic_id]);

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function update_facebook_pixel(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        $request->validate([
            'pixel_status' => 'required',
            'pixel_app_id' => 'required',
        ], [
            'pixel_status.required' => __('Status is required'),
            'pixel_app_id.required' => __('App id is required'),
        ]);

        Setting::where('key', 'pixel_status')->update(['value' => $request->pixel_status]);
        Setting::where('key', 'pixel_app_id')->update(['value' => $request->pixel_app_id]);

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function update_social_login(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        $rules = [
            'facebook_login_status' => 'required',
            'facebook_app_id' => 'required',
            'facebook_app_secret' => 'required',
            'facebook_redirect_url' => 'required',
            'google_login_status' => 'required',
            'gmail_client_id' => 'required',
            'gmail_secret_id' => 'required',
            'gmail_redirect_url' => 'required',
        ];
        $customMessages = [
            'facebook_login_status.required' => __('Facebook Status is required'),
            'facebook_app_id.required' => __('Facebook app id is required'),
            'facebook_app_secret.required' => __('App secret is required'),
            'facebook_redirect_url.required' => __('Facebook redirect url is required'),
            'google_login_status.required' => __('Google is required'),
            'gmail_client_id.required' => __('Google client is required'),
            'gmail_secret_id.required' => __('Google secret is required'),
            'gmail_redirect_url.required' => __('Google redirect url is required'),
        ];
        $request->validate($rules, $customMessages);

        Setting::where('key', 'facebook_login_status')->update(['value' => $request->facebook_login_status]);
        Setting::where('key', 'facebook_app_id')->update(['value' => $request->facebook_app_id]);
        Setting::where('key', 'facebook_app_secret')->update(['value' => $request->facebook_app_secret]);
        Setting::where('key', 'facebook_redirect_url')->update(['value' => $request->facebook_redirect_url]);
        Setting::where('key', 'google_login_status')->update(['value' => $request->google_login_status]);
        Setting::where('key', 'gmail_client_id')->update(['value' => $request->gmail_client_id]);
        Setting::where('key', 'gmail_secret_id')->update(['value' => $request->gmail_secret_id]);
        Setting::where('key', 'gmail_redirect_url')->update(['value' => $request->gmail_redirect_url]);

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);

    }

    public function update_pusher(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        $request->validate([
            'pusher_status' => 'required',
            'pusher_app_id' => 'required',
            'pusher_app_key' => 'required',
            'pusher_app_secret' => 'required',
            'pusher_app_cluster' => 'required',
        ], [
            'pusher_status.required' => __('Status is required'),
            'pusher_app_id.required' => __('Pusher App id is required'),
            'pusher_app_key.required' => __('Pusher App Key is required'),
            'pusher_app_secret.required' => __('Pusher App Secret is required'),
            'pusher_app_cluster.required' => __('Pusher App Cluster is required'),
        ]);

        Setting::where('key', 'pusher_status')->update(['value' => $request->pusher_status]);
        Setting::where('key', 'pusher_app_id')->update(['value' => $request->pusher_app_id]);
        Setting::where('key', 'pusher_app_key')->update(['value' => $request->pusher_app_key]);
        Setting::where('key', 'pusher_app_secret')->update(['value' => $request->pusher_app_secret]);
        Setting::where('key', 'pusher_app_cluster')->update(['value' => $request->pusher_app_cluster]);

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function seo_setting()
    {
        checkAdminHasPermissionAndThrowException('setting.view');
        $pages = SeoSetting::all();

        return view('globalsetting::seo_setting', compact('pages'));
    }

    public function update_seo_setting(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        $rules = [
            'seo_title' => 'required',
            'seo_description' => 'required',
        ];
        $customMessages = [
            'seo_title.required' => __('SEO title is required'),
            'seo_description.required' => __('SEO description is required'),
        ];
        $request->validate($rules, $customMessages);

        $page = SeoSetting::find($id);
        $page->seo_title = $request->seo_title;
        $page->seo_description = $request->seo_description;
        $page->save();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);

    }

    public function cache_clear()
    {
        checkAdminHasPermissionAndThrowException('setting.update');

        return view('globalsetting::cache_clear');
    }

    public function cache_clear_confirm()
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        Artisan::call('optimize:clear');

        $notification = __('Cache cleared successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function database_clear()
    {
        checkAdminHasPermissionAndThrowException('setting.view');

        return view('globalsetting::database_clear');
    }

    public function database_clear_success()
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        // truncate all model here

        $notification = __('Database Cleared Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);

    }

    public function put_setting_cache()
    {
        $setting_info = Setting::get();

        $setting = [];
        foreach ($setting_info as $setting_item) {
            $setting[$setting_item->key] = $setting_item->value;
        }

        $setting = (object) $setting;

        Cache::put('setting', $setting);
    }

    public function customCode()
    {
        checkAdminHasPermissionAndThrowException('setting.view');
        $customCode = CustomCode::first();
        if (! $customCode) {
            $customCode = new CustomCode();
            $customCode->css = '//write your css code here without the style tag';
            $customCode->javascript = '//write your javascript here without the script tag';
            $customCode->save();
        }

        return view('globalsetting::custom_code', compact('customCode'));
    }

    public function customCodeUpdate(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        $customCode = CustomCode::first();
        if (! $customCode) {
            $customCode = new CustomCode();
        }
        $customCode->css = $request->css;
        $customCode->javascript = $request->javascript;
        $customCode->save();

        $notification = __('Updated Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function update_maintenance_mode_status()
    {
        checkAdminHasPermissionAndThrowException('setting.update');
        $status = $this->cachedSetting?->maintenance_mode == 1 ? 0 : 1;

        Setting::where('key', 'maintenance_mode')->update(['value' => $status]);

        $this->put_setting_cache();

        return response()->json([
            'success' => true,
            'message' => __('Updated Successfully'),
        ]);
    }

    public function update_maintenance_mode(Request $request)
    {
        checkAdminHasPermissionAndThrowException('setting.update');

        $request->validate([
            'maintenance_title' => 'required',
            'maintenance_description' => 'required',
        ], [
            'maintenance_title' => __('Maintenance Mode Title is required'),
            'maintenance_description' => __('Maintenance Mode Description is required'),
        ]);

        Setting::where('key', 'maintenance_title')->update(['value' => $request->maintenance_title]);
        Setting::where('key', 'maintenance_description')->update(['value' => $request->maintenance_description]);

        $this->put_setting_cache();

        $notification = __('Update Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function systemUpdate()
    {
        $zipLoaded = extension_loaded('zip');
        $updateAvailablity = null;

        if (request('type') == 'check') {
            Cache::forget('update_url');
        }

        if (function_exists('showUpdateAvailablity')) {
            $this->put_setting_cache();
            $updateAvailablity = showUpdateAvailablity();
        }

        $updateFileDetails = false;
        $files = false;
        $uploadFileSize = false;

        $zipFilePath = public_path('upload/update.zip');
        if ($updateFileDetails = File::exists($zipFilePath)) {
            $uploadFileSize = File::size($zipFilePath);

            $files = $this->getFilesFromZip($zipFilePath);
        }

        return view('globalsetting::auto-update', compact('updateAvailablity', 'updateFileDetails', 'uploadFileSize', 'files', 'zipLoaded'));
    }

    public function systemUpdateStore(Request $request)
    {
        $request->validate([
            'zip_file' => 'required|mimes:zip',
        ]);

        $zipFilePath = public_path('upload/update.zip');

        if (File::exists($zipFilePath)) {
            File::delete($zipFilePath);
        }

        // Store the uploaded file
        $zipFile = $request->file('zip_file');
        $zipFilePath = $zipFile->move(public_path('upload'), 'update.zip');

        if (!$this->isFirstDirUpload($zipFilePath)) {
            File::delete($zipFilePath);
            $notification = __('Invalid Update File Structure');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return redirect()->back()->with($notification);
        }

        return back();
    }

    public function systemUpdateRedirect()
    {
        $zipFilePath = public_path('upload/update.zip');

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath) !== true) {
            File::delete($zipFilePath);
            $notification = __('Corrupted Zip File');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            $zip->close();
            return redirect()->back()->with($notification);
        }

        if (!$this->isFirstDirUpload($zipFilePath)) {
            $notification = __('Invalid Update File Structure');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            $zip->close();
            return redirect()->back()->with($notification);
        }

        $zip->close();

        $this->deleteFolderAndFiles(base_path('update'));

        if ($zip->open($zipFilePath) === true) {
            $zip->extractTo(base_path());
            $zip->close();
        }

        return redirect(url('/update'));
    }

    public function systemUpdateDelete()
    {
        $zipFilePath = public_path('upload/update.zip');
        File::delete($zipFilePath);

        $this->deleteFolderAndFiles(base_path('update'));

        $notification = __('Deleted Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];
        return back()->with($notification);
    }

    private function getFilesFromZip($zipFilePath)
    {
        $files = [];
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $fileInfo = $zip->statIndex($i);
                $files[] = $fileInfo['name'];
            }
        }
        $zip->close();
        return $files;
    }

    private function deleteFolderAndFiles($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . '/' . $file;

            if (is_dir($path)) {
                $this->deleteFolderAndFiles($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }

    private function isFirstDirUpload($zipFilePath)
    {
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath) === TRUE) {
            $firstDir = null;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $fileInfo = $zip->statIndex($i);
                $filePathParts = explode('/', $fileInfo['name']);

                if (count($filePathParts) > 1) {
                    $firstDir = $filePathParts[0];
                    break;
                }
            }

            $zip->close();
            return $firstDir === "update";
        }

        $zip->close();
        return false;
    }
}
