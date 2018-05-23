<?php

use Illuminate\Http\Request;

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
Route::post('login', 'ApiLoginController@login');

Route::middleware('jwt.auth')->group(function() {

    Route::apiResource('/user', 'UserController');
    Route::apiResource('/usergroup', 'UserGroupController');
    Route::apiResource('/permission', 'PermissionController');
    Route::apiResource('/setting', 'SettingController');
    Route::apiResource('/settinggroup', 'SettingGroupController');

});
