<?php

use Illuminate\Support\Facades\Route;
use Modules\SupportTicket\app\Http\Controllers\SupportTicketController;

Route::middleware(['auth:admin', 'translation'])
    ->prefix('admin/support')
    ->name('admin.support.')
    ->controller(SupportTicketController::class)
    ->group(function () {
        Route::get('ticket', 'index')->name('ticket');
        Route::get('ticket-show/{id}', 'show')->name('ticket-show');
        Route::delete('ticket-delete/{id}', 'destroy')->name('ticket-delete');
        Route::put('ticket-closed/{id}', 'closed')->name('ticket-closed');
        Route::post('store-ticket-message', 'store_message')->name('store-ticket-message');
    });
