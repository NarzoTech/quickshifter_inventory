<?php

use App\Exceptions\AccessPermissionDeniedException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Modules\BasicPayment\app\Models\BasicPayment;
use Modules\GlobalSetting\app\Models\Setting;
use Modules\Language\app\Models\Language;
use Modules\PaymentGateway\app\Models\PaymentGateway;
use Nwidart\Modules\Facades\Module;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

function file_upload(UploadedFile $file, string $path = 'uploads/custom-images/', string|null $oldFile = '', bool $optimize = false)
{
    $extention = $file->getClientOriginalExtension();
    $file_name = 'wsus-img' . date('-Y-m-d-h-i-s-') . rand(999, 9999) . '.' . $extention;
    $file_name = $path . $file_name;
    $file->move(public_path($path), $file_name);

    try {
        if ($oldFile && !str($oldFile)->contains('uploads/website-images') && File::exists(public_path($oldFile))) {
            unlink(public_path($oldFile));
        }

        if ($optimize) {
            ImageOptimizer::optimize(public_path($file_name));
        }
    } catch (Exception $e) {
        Log::info($e->getMessage());
    }

    return $file_name;
}
// file upload method
if (!function_exists('allLanguages')) {
    function allLanguages()
    {
        $allLanguages = Cache::rememberForever('allLanguages', function () {
            return Language::select('code', 'name', 'direction', 'status')->get();
        });

        if (!$allLanguages) {
            $allLanguages = Language::select('code', 'name', 'direction', 'status')->get();
        }

        return $allLanguages;
    }
}

if (!function_exists('getSessionLanguage')) {
    function getSessionLanguage(): string
    {
        if (!session()->has('lang')) {
            session()->put('lang', config('app.locale'));
            session()->forget('text_direction');
            session()->put('text_direction', 'ltr');
        }

        $lang = Session::get('lang');

        return $lang;
    }
}

function admin_lang()
{
    return Session::get('admin_lang');
}

// calculate currency
function currency($price)
{
    // currency information will be loaded by Session value

    // $currency_icon = Session::get('currency_icon');
    // $currency_code = Session::get('currency_code');
    // $currency_rate = Session::get('currency_rate');
    // $currency_position = Session::get('currency_position');

    $currency_icon = '$';
    $currency_code = 'USD';
    $currency_rate = '1.00';
    $currency_position = 'before_price';

    $price = $price * $currency_rate;
    $price = number_format($price, 2, '.', ',');

    if ($currency_position == 'before_price') {
        $price = $currency_icon . $price;
    } elseif ($currency_position == 'before_price_with_space') {
        $price = $currency_icon . ' ' . $price;
    } elseif ($currency_position == 'after_price') {
        $price = $price . $currency_icon;
    } elseif ($currency_position == 'after_price_with_space') {
        $price = $price . ' ' . $currency_icon;
    } else {
        $price = $currency_icon . $price;
    }

    return $price;
}

// calculate currency

// custom decode and encode input value
function html_decode($text)
{
    $after_decode = htmlspecialchars_decode($text, ENT_QUOTES);

    return $after_decode;
}

if (!function_exists('checkAdminHasPermission')) {
    function checkAdminHasPermission($permission): bool
    {
        return Auth::guard('admin')->user()->can($permission) ? true : false;
    }
}

if (!function_exists('checkAdminHasPermissionAndThrowException')) {
    function checkAdminHasPermissionAndThrowException($permission)
    {
        if (!checkAdminHasPermission($permission)) {
            throw new AccessPermissionDeniedException();
        }
    }
}

if (!function_exists('getSettingStatus')) {
    function getSettingStatus($key)
    {
        if (Cache::has('setting')) {
            $setting = Cache::get('setting');
            if (!is_null($key)) {
                return $setting->$key == 'active' ? true : false;
            }
        } else {
            try {
                return Setting::where('key', $key)->first()?->value == 'active' ? true : false;
            } catch (Exception $e) {
                    Log::info($e->getMessage());
                return false;
            }
        }

        return false;
    }
}
if (!function_exists('checkCrentials')) {
    function checkCrentials()
    {
        if (Cache::has('setting') && $settings = Cache::get('setting')) {
            if ($settings->recaptcha_status !== 'inactive' && ($settings->recaptcha_site_key == 'recaptcha_site_key' || $settings->recaptcha_secret_key == 'recaptcha_secret_key' || $settings->recaptcha_site_key == '' || $settings->recaptcha_secret_key == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Google Recaptcha credentails not found'),
                    'description' => __('This may create a problem while submitting any form submission from website. Please fill up the credential from google account.'),
                    'route'       => 'admin.crediential-setting',
                ];
            }

            if ($settings->pixel_status !== 'inactive' && ($settings->pixel_app_id == 'pixel_app_id' || $settings->pixel_app_id == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Facebook Pixel credentails not found'),
                    'description' => __('This may create a problem to analyze your website. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.crediential-setting',
                ];
            }

            if ($settings->facebook_login_status !== 'inactive' && ($settings->facebook_app_id == 'facebook_app_id' || $settings->facebook_app_secret == 'facebook_app_secret' || $settings->facebook_redirect_url == 'facebook_redirect_url' || $settings->facebook_app_id == '' || $settings->facebook_app_secret == '' || $settings->facebook_redirect_url == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Facebook login credentails not found'),
                    'description' => __('This may create a problem while logging in using facebook. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.crediential-setting',
                ];
            }

            if ($settings->google_login_status !== 'inactive' && ($settings->gmail_client_id == 'gmail_client_id' || $settings->gmail_secret_id == 'gmail_secret_id' || $settings->gmail_redirect_url == 'gmail_redirect_url' || $settings->gmail_client_id == '' || $settings->gmail_secret_id == '' || $settings->gmail_redirect_url == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Google login credentails not found'),
                    'description' => __('This may create a problem while logging in using google. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.crediential-setting',
                ];
            }

            if ($settings->google_analytic_status !== 'inactive' && ($settings->google_analytic_id == 'google_analytic_id' || $settings->google_analytic_id == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Google Analytic credentails not found'),
                    'description' => __('This may create a problem to analyze your website. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.crediential-setting',
                ];
            }

            if ($settings->tawk_status !== 'inactive' && ($settings->tawk_chat_link == 'tawk_chat_link' || $settings->tawk_chat_link == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Tawk Chat Link credentails not found'),
                    'description' => __('This may create a problem to analyze your website. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.crediential-setting',
                ];
            }

            if ($settings->pusher_status !== 'inactive' && ($settings->pusher_app_id == 'pusher_app_id' || $settings->pusher_app_key == 'pusher_app_key' || $settings->pusher_app_secret == 'pusher_app_secret' || $settings->pusher_app_cluster == 'pusher_app_cluster' || $settings->pusher_app_id == '' || $settings->pusher_app_key == '' || $settings->pusher_app_secret == '' || $settings->pusher_app_cluster == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Pusher credentails not found'),
                    'description' => __('This may create a problem while logging in using google. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.crediential-setting',
                ];
            }

            if ($settings->mail_host == 'mail_host' || $settings->mail_username == 'mail_username' || $settings->mail_password == 'mail_password' || $settings->mail_host == '' || $settings->mail_port == '' || $settings->mail_username == '' || $settings->mail_password == '') {
                return (object) [
                    'status'      => true,
                    'message'     => __('Mail credentails not found'),
                    'description' => __('This may create a problem while sending email. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.email-configuration',
                ];
            }
        }

        if (!Cache::has('basic_payment') && Module::isEnabled('BasicPayment')) {
            Cache::rememberForever('basic_payment', function () {
                $payment_info = BasicPayment::get();
                $basic_payment = [];
                foreach ($payment_info as $payment_item) {
                    $basic_payment[$payment_item->key] = $payment_item->value;
                }

                return (object) $basic_payment;
            });
        }

        if (Cache::has('basic_payment') && $basicPayment = Cache::get('basic_payment')) {
            if ($basicPayment->stripe_status !== 'inactive' && ($basicPayment->stripe_key == 'stripe_key' || $basicPayment->stripe_secret == 'stripe_secret' || $basicPayment->stripe_key == '' || $basicPayment->stripe_secret == '')) {

                return (object) [
                    'status'      => true,
                    'message'     => __('Stripe credentails not found'),
                    'description' => __('This may create a problem while making payment. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.basicpayment',
                ];
            }

            if ($basicPayment->paypal_status !== 'inactive' && ($basicPayment->paypal_client_id == 'paypal_client_id' || $basicPayment->paypal_secret_key == 'paypal_secret_key' || $basicPayment->paypal_client_id == '' || $basicPayment->paypal_secret_key == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Paypal credentails not found'),
                    'description' => __('This may create a problem while making payment. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.basicpayment',
                ];
            }
        }

        if (!Cache::has('payment_setting') && Module::isEnabled('PaymentGateway')) {
            Cache::rememberForever('payment_setting', function () {
                $payment_info = PaymentGateway::get();
                $payment_setting = [];
                foreach ($payment_info as $payment_item) {
                    $payment_setting[$payment_item->key] = $payment_item->value;
                }

                return (object) $payment_setting;
            });
        }

        if (Cache::has('payment_setting') && $paymentAddons = Cache::get('payment_setting')) {
            if ($paymentAddons->razorpay_status !== 'inactive' && ($paymentAddons->razorpay_key == 'razorpay_key' || $paymentAddons->razorpay_secret == 'razorpay_secret' || $paymentAddons->razorpay_key == '' || $paymentAddons->razorpay_secret == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Razorpay credentails not found'),
                    'description' => __('This may create a problem while making payment. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.paymentgateway',
                ];
            }

            if ($paymentAddons->flutterwave_status !== 'inactive' && ($paymentAddons->flutterwave_public_key == 'flutterwave_public_key' || $paymentAddons->flutterwave_secret_key == 'flutterwave_secret_key' || $paymentAddons->flutterwave_public_key == '' || $paymentAddons->flutterwave_secret_key == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Flutterwave credentails not found'),
                    'description' => __('This may create a problem while making payment. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.paymentgateway',
                ];
            }

            if ($paymentAddons->paystack_status !== 'inactive' && ($paymentAddons->paystack_public_key == 'paystack_public_key' || $paymentAddons->paystack_secret_key == 'paystack_secret_key' || $paymentAddons->paystack_public_key == '' || $paymentAddons->paystack_secret_key == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Paystack credentails not found'),
                    'description' => __('This may create a problem while making payment. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.paymentgateway',
                ];
            }

            if ($paymentAddons->mollie_status !== 'inactive' && ($paymentAddons->mollie_key == 'mollie_key' || $paymentAddons->mollie_key == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Mollie credentails not found'),
                    'description' => __('This may create a problem while making payment. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.paymentgateway',
                ];
            }

            if ($paymentAddons->instamojo_status !== 'inactive' && ($paymentAddons->instamojo_api_key == 'instamojo_api_key' || $paymentAddons->instamojo_auth_token == 'instamojo_auth_token' || $paymentAddons->instamojo_api_key == '' || $paymentAddons->instamojo_auth_token == '')) {
                return (object) [
                    'status'      => true,
                    'message'     => __('Instamojo credentails not found'),
                    'description' => __('This may create a problem while making payment. Please fill up the credential to avoid any problem.'),
                    'route'       => 'admin.paymentgateway',
                ];
            }
        }

        return false;
    }
}

if (!function_exists('isRoute')) {
    function isRoute(string|array $route, string $returnValue = null)
    {
        if (is_array($route)) {
            foreach ($route as $value) {
                if (Route::is($value)) {
                    return is_null($returnValue) ? true : $returnValue;
                }
            }
            return false;
        }

        if (Route::is($route)) {
            return is_null($returnValue) ? true : $returnValue;
        }

        return false;
    }
}
