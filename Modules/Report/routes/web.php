<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\app\Http\Controllers\OtherSummeryController;
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

Route::group(['as' => 'admin.', 'prefix' => getAdminRoutePrefix(), 'middleware' => ['auth:admin', 'translation']], function () {
    Route::get('report/other-income', [ReportController::class, 'otherIncome'])->name('report.other-income');
    // daily transaction summary
    Route::get('report/dts', [ReportController::class, 'dts'])->name('report.dts');


    Route::get('report/barcode-wise-product', [ReportController::class, 'barcodeWiseProduct'])->name('report.barcode-wise-product');
    Route::get('report/barcode-sale', [ReportController::class, 'barcodeSale'])->name('report.barcode-sale');
    Route::get('report/categories', [ReportController::class, 'categories'])->name('report.categories');
    Route::get('report/customers', [ReportController::class, 'customers'])->name('report.customers');
    Route::get('report/receivable', [ReportController::class, 'receivable'])->name('report.receivable');
    Route::get('report/details-sale', [ReportController::class, 'detailsSale'])->name('report.details-sale');
    Route::get('report/due-date-sale', [ReportController::class, 'dueDateSale'])->name('report.due-date-sale');
    Route::get('report/expense', [ReportController::class, 'expense'])->name('report.expense');
    Route::get('report/master-sale', [ReportController::class, 'masterSale'])->name('report.master-sale');
    Route::get('report/monthly-sale', [ReportController::class, 'masterSale'])->name('report.monthly-sale');
    Route::get('report/profit-loss', [ReportController::class, 'profitLoss'])->name('report.profit-loss');
    Route::get('report/product-sale-report', [ReportController::class, 'productSaleReport'])->name('report.product-sale-report');
    Route::get('report/received-report', [ReportController::class, 'receivedReport'])->name('report.received-report');
    Route::get('report/purchase', [ReportController::class, 'purchase'])->name('report.purchase');
    Route::get('report/supplier', [ReportController::class, 'supplier'])->name('report.supplier');
    Route::get('report/salary', [ReportController::class, 'salary'])->name('report.salary');
    Route::get('report/supplier-payment', [ReportController::class, 'supplierPayment'])->name('report.supplier-payment');
    Route::get('report/other-sales', [ReportController::class, 'otherSales'])->name('report.other-sales');


    Route::get('other-summery/customer', [OtherSummeryController::class, 'customer'])->name('other-summery.customer');

    Route::post('other-summery/customer', [OtherSummeryController::class, 'customerStore'])->name('other-summery.customer.store');
    Route::get('other-summery/customer/{id}/ledger', [OtherSummeryController::class, 'customerLedger'])->name('other-summery.customer.ledger');

    Route::put('other-summery/customer/{id}', [OtherSummeryController::class, 'customerUpdate'])->name('other-summery.customer.update');

    Route::delete('other-summery/customer/{id}', [OtherSummeryController::class, 'customerDelete'])->name('other-summery.customer.delete');
    Route::put('other-summery/pay-due', [OtherSummeryController::class, 'payDue'])->name('other-summery.pay-due');


    // supplier
    Route::get('other-summery/supplier', [OtherSummeryController::class, 'supplier'])->name('other-summery.supplier');
    Route::post('other-summery/supplier', [OtherSummeryController::class, 'supplierStore'])->name('other-summery.supplier.store');
    Route::get('other-summery/supplier/{id}/ledger', [OtherSummeryController::class, 'supplierLedger'])->name('other-summery.supplier.ledger');

    Route::put('other-summery/supplier/{id}', [OtherSummeryController::class, 'supplierUpdate'])->name('other-summery.supplier.update');

    Route::delete('other-summery/supplier/{id}', [OtherSummeryController::class, 'supplierDelete'])->name('other-summery.supplier.delete');
});
