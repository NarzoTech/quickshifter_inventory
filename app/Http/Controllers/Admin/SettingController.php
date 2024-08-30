<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\GlobalSetting\app\Enums\AllTimeZoneEnum;
use Modules\GlobalSetting\app\Enums\CountryEnum;

class SettingController extends Controller
{
    public function settings()
    {
        $all_timezones = AllTimeZoneEnum::getAll();
        $allCountries = CountryEnum::getAll();
        return view('admin.settings.settings', compact('all_timezones', 'allCountries'));
    }
}
