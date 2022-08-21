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
Route::controller(\App\Modules\Users\Controllers\UsersController::class)->group(function () {
    Route::post('users/search', 'search');
    Route::get('users/{id}', 'get');
    Route::post('users/{id}', 'update');
    Route::post('users', 'create');
    Route::delete('users/{id}', 'delete');
});