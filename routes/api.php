<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('auth/login', \App\Modules\Users\Controllers\UsersAuthController::class . '@login');
//Route::post('auth/register', \App\Modules\Users\Controllers\UsersAuthController::class . '@register');
Route::post('auth/password-recover', \App\Modules\Users\Controllers\UsersAuthController::class . '@passwordRecover');
Route::post('auth/password-reset', \App\Modules\Users\Controllers\UsersAuthController::class . '@changePasswordByToken');
Route::post('auth/verify-remember-token', \App\Modules\Users\Controllers\UsersAuthController::class . '@verifyRememberPassword');

Route::group(['middleware' => 'auth:api'], function () {
    // Auth Routes
    Route::post('auth/check', [\App\Modules\Users\Controllers\UsersAuthController::class, 'check']);
    Route::post('auth/logout', \App\Modules\Users\Controllers\UsersAuthController::class . '@logout');

    // Users Routes
    Route::controller(\App\Modules\Users\Controllers\UsersController::class)->group(function () {
        Route::post('users/change-password', 'changePassword');
        Route::post('users/search', 'search');
        Route::get('users/me', 'getSelf');
        Route::post('users/me', 'updateSelf');
        Route::get('users/{id}', 'get');
        Route::post('users/{id}', 'update');
        Route::post('users', 'create');
        Route::delete('users/{id}', 'delete');
    });
});
