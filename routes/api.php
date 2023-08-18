<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->namespace('App\\Http\\Controllers')->group(function () {
    // Auth group
    Route::prefix('auth')->group(function () {
        Route::post('login', 'AuthController@login');
        Route::post('register', 'AuthController@register');
    });

    Route::prefix('users')->middleware(['auth:api'])->group(function() {
        Route::get('profile', 'UserController@getProfile');
    });
});
