<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounts\app\Http\Controllers\AccountsController;
use Modules\Accounts\app\Http\Controllers\BankController;

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
    Route::resource('accounts', AccountsController::class)->names('accounts');
    Route::resource('bank', BankController::class)->names('bank');
});
