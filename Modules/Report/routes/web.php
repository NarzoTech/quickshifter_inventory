<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\app\Http\Controllers\ReportController;

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
    Route::get('report/other-income', [ReportController::class, 'otherIncome'])->name('report.other-income');
    // daily transaction summary
    Route::get('report/dts', [ReportController::class, 'dts'])->name('report.dts');


    Route::get('report/barcode-wise-product', [ReportController::class, 'barcodeWiseProduct'])->name('report.barcode-wise-product');
    Route::get('report/barcode-sale', [ReportController::class, 'barcodeSale'])->name('report.barcode-sale');
    Route::get('report/categories', [ReportController::class, 'categories'])->name('report.categories');
});
