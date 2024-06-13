<?php

use Illuminate\Support\Facades\Route;
use Modules\Supplier\app\Http\Controllers\SupplierController;
use Modules\Supplier\app\Http\Controllers\SupplierGroupController;

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

Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {
    Route::resource('suppliers', SupplierController::class);
    Route::get('suppliers/due-pay/{id}', [SupplierController::class, 'duePay'])->name('suppliers.due-pay');
    Route::resource('supplierGroup', SupplierGroupController::class);
});
