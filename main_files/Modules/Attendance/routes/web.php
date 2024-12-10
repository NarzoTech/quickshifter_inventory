<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\app\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth:admin', 'translation'])
    ->name('admin.')
    ->prefix('admin')
    ->group(function () {
        Route::resource('attendance', AttendanceController::class)->only('index', 'create', 'store')->names('attendance');

        Route::get('attendance/settings/weekdays', [AttendanceController::class, 'weekDays'])->name('attendance.settings.weekdays');

        Route::put('attendance/settings/weekdays/{id}', [AttendanceController::class, 'weekDaysUpdate'])->name('attendance.settings.weekdays.update');
    });
