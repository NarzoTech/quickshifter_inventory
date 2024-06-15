<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchase\app\Http\Controllers\PurchaseController;
use Modules\Purchase\app\Http\Controllers\PurchaseReturnTypeController;

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
    Route::resource('purchase', PurchaseController::class)->names('purchase');
    Route::get('purchase/return/list', [PurchaseReturnTypeController::class, 'index'])->name('purchase.return.list');

    Route::post('purchase/product', [PurchaseController::class, 'product'])->name('purchase.product');
});
