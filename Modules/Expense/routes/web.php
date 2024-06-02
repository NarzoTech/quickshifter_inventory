<?php

use Illuminate\Support\Facades\Route;
use Modules\Expense\app\Http\Controllers\ExpenseController;
use Modules\Expense\app\Http\Controllers\ExpenseTypeController;

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
    Route::resource('expense', ExpenseController::class)->names('expense');
    Route::resource('expenseType', ExpenseTypeController::class)->names('expense.type');
});
