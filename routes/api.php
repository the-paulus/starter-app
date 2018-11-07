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

    Route::get('/user/auth_types', 'UserController@authentication_types');
    Route::apiResource('/user', 'UserController');
    Route::match(['get', 'post'], '/user/search', 'UserController@search');
    Route::get('/user/me/{detail?}', 'UserController@userInfo');
    Route::get('/user/emulate/{id}', 'LoginController@emulateUser');
    Route::apiResource('/usergroup', 'UserGroupController');
    Route::match(['get', 'post'], '/usergroup/search', 'UserGroupController@search');
    Route::apiResource('/permission', 'PermissionController');
    Route::match(['get', 'post'], '/permission/search', 'PermissionController@search');
    Route::apiResource('/setting', 'SettingController');
    Route::match(['get', 'post'], '/setting/search', 'SettingController@search');
    Route::apiResource('/settinggroup', 'SettingGroupController');

});
