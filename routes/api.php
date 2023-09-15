<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->name('v1.')->group(function ()
{
    Route::prefix('auth')->name('auth.')->controller(AuthController::class)->group(function ()
    {
        Route::post('login', 'login')->name('login');
        Route::get('logout', 'logout')->name('logout')->middleware(['jwt.auth']);
    });
    Route::middleware(['jwt.auth'])->group(function ()
    {
        Route::middleware(['access.admin'])->group(function ()
        {
            Route::prefix('admin')->name('admin.')->controller(AdminController::class)->group(function ()
            {
                Route::get('user-listing', 'userListing')->name('user_listing');
                Route::put('user-edit/{uuid}', 'userEdit')->name('user_edit');
                Route::delete('user-delete/{uuid}', 'userDelete')->name('user_delete');
            });
            Route::apiResources(['admin' => AdminController::class]);
        });
        Route::middleware(['access.user'])->group(function ()
        {
            Route::prefix('user')->name('user.')->controller(UserController::class)->group(function ()
            {
                Route::get('orders/{uuid}', 'orders')->name('orders');
                Route::post('forgot-password', 'forgotPassword')->name('forgot_password');
                Route::post('reset-password-token', 'resetPassword')->name('reset_password');
            });
            Route::apiResources(['user' => UserController::class]);
        });
        Route::prefix('payments')->name('payments.')->group(function ()
        {

        });
        Route::prefix('order-statuses')->name('order_statuses.')->group(function ()
        {

        });
        Route::prefix('orders')->name('orders.')->group(function ()
        {

        });
    });
});
