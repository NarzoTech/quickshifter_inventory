<?php

use Illuminate\Support\Facades\Route;
use Modules\MenuBuilder\app\Http\Controllers\MenuBuilderController;

Route::middleware(['auth:admin', 'translation'])
    ->prefix('admin')
    ->name('admin.')
    ->controller(MenuBuilderController::class)
    ->group(function () {
        Route::get('menus/{code}', 'index')->name('menus.index');
        Route::post('menus', 'store')->name('menus.store');
        Route::post('menus/update', 'update')->name('menus.update');
        Route::post('menus/delete', 'destroy')->name('menus.destroy');
        Route::put('menus/update-status/{menu}', 'updateStatus')->name('menus.update-status');
        Route::get('/menubuilder/sortmenu', [MenuBuilderController::class, 'sortmenu'])->name('sortmenu');
    });
