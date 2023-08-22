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

    // Login required
    Route::middleware(['auth:api'])->group(function () {
        Route::prefix('users')->group(function() {
            Route::get('profile', 'UserController@getProfile');
            Route::put('profile', 'UserController@updateProfile');

            // staff area (rt, rw, lurah)
            Route::middleware(['staff_area'])->group(function () {
                Route::get('lists', 'UserController@getCivilianList');
                Route::get('details/{user}', 'UserController@getCivilianDetail');
                Route::put('approval/{user}', 'UserController@profileApproval');
            });
        });

        // lurah area (lurah only)
        Route::prefix('staff')->middleware(['lurah_area'])->group(function () {
            Route::get('lists', 'StaffController@getStaffList');
            Route::get('details/{user}', 'StaffController@getStaffDetail');
            Route::post('register', 'StaffController@register');
        });
    });
});
