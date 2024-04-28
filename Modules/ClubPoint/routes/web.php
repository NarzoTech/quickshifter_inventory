<?php

use Illuminate\Support\Facades\Route;
use Modules\ClubPoint\app\Http\Controllers\Admin\ClubPointController as AdminClubPointController;
use Modules\ClubPoint\app\Http\Controllers\ClubPointController;

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'translation']], function () {
    Route::get('clubpoint-setting', [AdminClubPointController::class, 'index'])->name('clubpoint-setting');
    Route::put('update-clubpoint-setting', [AdminClubPointController::class, 'update'])->name('update-clubpoint-setting');
    Route::get('clubpoint-history', [AdminClubPointController::class, 'history'])->name('clubpoint-history');
});

Route::group(['middleware' => ['auth:web']], function () {
    Route::get('clubpoints', [ClubPointController::class, 'index'])->name('clubpoints');
    Route::get('clubpoint-convert/{id}', [ClubPointController::class, 'clubpoint_convert'])->name('clubpoint-convert');
});
