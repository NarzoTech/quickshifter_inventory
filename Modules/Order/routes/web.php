<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\app\Http\Controllers\OrderController;

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

    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index')->name('orders');
        Route::get('/pending-orders', 'pending_order')->name('pending-orders');
        Route::get('/order/{id}', 'show')->name('order');
        Route::post('/order-payment-reject/{id}', 'order_payment_reject')->name('order-payment-reject');
        Route::post('/order-payment-approved/{id}', 'order_payment_approved')->name('order-payment-approved');
        Route::delete('/order-delete/{id}', 'destroy')->name('order-delete');
        Route::get('/pending-payment', 'pending_payment')->name('pending-payment');
        Route::get('/rejected-payment', 'rejected_payment')->name('rejected-payment');
    });
});
