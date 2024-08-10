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
use Modules\Currency\app\Models\MultiCurrency;
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

if (!function_exists('delete_file')) {
    function delete_file($path)
    {
        if (File::exists(public_path($path))) {
            unlink(public_path($path));
        }
    }
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

if (!function_exists('accountList')) {
    function accountList()
    {
        $list = [
            'cash' => 'Cash',
            'bank' => 'Bank',
            'mobile_banking' => 'Mobile Banking',
            'card' => 'Card',
        ];

        return $list;
    }
}

if (!function_exists('mobileBankList')) {
    function mobileBankList()
    {
        $list = [
            'bkash' => 'bKash',
            'rocket' => 'Rocket',
            'nagad' => 'Nagad',
            'surecash' => 'SureCash',
            'ucash' => 'UCash',
            'mCash' => 'mCash',
            'tap' => 'Tap',
        ];

        return $list;
    }
}

// card type
if (!function_exists('cardTypeList')) {
    function cardTypeList()
    {
        $list = [
            'mastercard' => 'MasterCard',
            'visa' => 'Visa',
            'amex' => 'American Express',
            'nexus' => 'Nexus',
            'credit' => 'Credit Card',
            'debit' => 'Debit Card',
            'prepaid' => 'Prepaid Card',

        ];

        return $list;
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

// all payment methods
if (!function_exists('allPaymentMethods')) {
    function allPaymentMethods($key = null)
    {
        $methods = [
            'bkash' => 'bKash',
            'rocket' => 'Rocket',
            'nagad' => 'Nagad',
            'bank_transfer' => 'Bank Transfer',
            'hand_cash' => 'Hand Cash',
            'cod' => 'Cash On Delivery',
            'check' => 'Bank Check',
        ];

        if ($key) {
            return $methods[$key];
        }

        return $methods;
    }
}
function admin_lang()
{
    return Session::get('admin_lang');
}


// calculate currency
function currency($price = '')
{
    // currency information will be loaded by Session value

    $currencySetting = Cache::rememberForever('currency', function () {
        $siteCurrencyId = Session::get('site_currency');

        $currency = MultiCurrency::when($siteCurrencyId, function ($query) use ($siteCurrencyId) {
            return $query->where('id', $siteCurrencyId);
        })->when(!$siteCurrencyId, function ($query) {
            return $query->where('is_default', 'yes');
        })->first();

        return $currency;
    });

    $currency_icon = $currencySetting->currency_icon;
    $currency_code = $currencySetting->currency_code;
    $currency_rate = $currencySetting->currency_rate ? $currencySetting->currency_rate : 1;
    $currency_position = $currencySetting->currency_position;
    if ($price) {
        $price = floatval(str_replace(',', '', $price));
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
    } else {
        return $currency_icon . '0.00';
    }
}


// get currency icon
function currency_icon()
{
    $currencySetting = Cache::rememberForever('currency', function () {
        $siteCurrencyId = Session::get('site_currency');

        $currency = MultiCurrency::when($siteCurrencyId, function ($query) use ($siteCurrencyId) {
            return $query->where('id', $siteCurrencyId);
        })->when(!$siteCurrencyId, function ($query) {
            return $query->where('is_default', 'yes');
        })->first();

        return $currency;
    });

    return $currencySetting->currency_icon;
}

// remove currency icon using regular expression
function remove_icon($price)
{
    $price = preg_replace('/[^0-9,.]/', '', $price);

    return $price;
}



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
