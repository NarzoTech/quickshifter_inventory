<?php

use Illuminate\Support\Facades\Route;
use Modules\PageBuilder\app\Http\Controllers\CustomizeablePageController;
use Modules\PageBuilder\app\Http\Controllers\CustomizeablePageItemController;

Route::middleware(['auth:admin', 'translation'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('custom-pages')
            ->name('custom-pages.')
            ->controller(CustomizeablePageController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                // Route::get('/create', 'create')->name('create');
                // Route::post('/store', 'store')->name('store');
                Route::get('/{page}/edit', 'edit')->name('edit');
                Route::put('/{page}/update', 'update')->name('update');
                Route::delete('/{page}', 'destroy')->name('destroy');
                Route::put('/status-update/{page}', 'statusUpdate')->name('update-status');
            });

        Route::prefix('custom-pages/items')
            ->name('custom-pages.items.')
            ->controller(CustomizeablePageItemController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                // Route::get('/create', 'create')->name('create');
                Route::post('/store', 'store')->name('store');
                // Route::get('/{item}/edit', 'edit')->name('edit');
                // Route::post('/{item}/update', 'update')->name('update');
                Route::delete('/{item}/delete', 'destroy')->name('destroy');
                Route::get('/{page}/sort', 'sortPageItems')->name('sortPageItems');
                Route::put('/status-update/{item}', 'statusUpdate')->name('update-status');
            });
    });
