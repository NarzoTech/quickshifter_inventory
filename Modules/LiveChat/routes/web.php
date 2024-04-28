<?php

use Illuminate\Support\Facades\Route;
use Modules\LiveChat\app\Http\Controllers\Admin\ChatSettingController;
use Modules\LiveChat\app\Http\Controllers\LiveChatController;

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'translation']], function () {
    Route::get('chat-setting', [ChatSettingController::class, 'index'])->name('chat-setting');
    Route::put('update-chat-setting', [ChatSettingController::class, 'update'])->name('update-chat-setting');
});

Route::group(['middleware' => ['auth:web']], function () {

    Route::controller(LiveChatController::class)->group(function () {
        Route::get('message-list', 'index')->name('message-list');
        Route::post('send-new-message', 'send_new_message')->name('send-new-message');
        Route::get('load-message-box/{id}', 'load_message_box')->name('load-message-box');
        Route::post('send-message', 'send_message')->name('send-message');
        Route::get('load-latest-message/{id}', 'load_latest_message')->name('load-latest-message');
        Route::get('get-new-contact-sender/{id}', 'get_new_contact_sender')->name('get-new-contact-sender');

    });
});
