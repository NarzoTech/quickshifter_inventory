<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\app\Http\Controllers\AreaController;
use Modules\Customer\app\Http\Controllers\CustomerController;
use Modules\Customer\app\Http\Controllers\CustomerGroupController;
use Modules\Customer\app\Http\Controllers\VehicleController;

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

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'translation']], function () {
    Route::resource('customers', CustomerController::class);
    Route::get('customers/single/{id}', [CustomerController::class, 'singleCustomer'])->name('customer.single');

    Route::resource('customerGroup', CustomerGroupController::class);
    Route::resource('vehicle', VehicleController::class);
    Route::resource('area', AreaController::class);
});
