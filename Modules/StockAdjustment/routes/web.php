<?php

use Illuminate\Support\Facades\Route;
use Modules\StockAdjustment\app\Http\Controllers\StockAdjustmentController;

Route::group(['as' => 'admin.', 'prefix' => getAdminRoutePrefix(), 'middleware' => ['auth:admin', 'translation']], function () {
    Route::get('stock-adjustment/product-search', [StockAdjustmentController::class, 'productSearch'])->name('stock-adjustment.product-search');
    Route::resource('stock-adjustment', StockAdjustmentController::class)->except(['edit', 'update']);
});
