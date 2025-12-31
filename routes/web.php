<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

//maintenance mode route
Route::get('/maintenance-mode', function () {
    $setting = Illuminate\Support\Facades\Cache::get('setting', null);
    if (! $setting?->maintenance_mode) {
        return redirect()->route('home');
    }

    return view('maintenance');
})->name('maintenance.mode');

//Profile route
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/user/update-password', [ProfileController::class, 'update_password'])->name('user.update-password');
});

Route::get('set-language', [DashboardController::class, 'setLanguage'])->name('set-language');

Route::post('apply-coupon', [PaymentController::class, 'apply_coupon'])->name('apply-coupon');

/**payment related route end */

require __DIR__ . '/admin.php';

Route::fallback(function () {
    abort(404);
});
