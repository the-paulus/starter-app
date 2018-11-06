<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('authenticated', 'Auth\\LoginController@authenticated');
Route::get('unauthorized', 'Auth\\LoginController@unauthorized');

Route::middleware('auth')->get('token', function() {
  return response()->json(['data' => ['token' => \Tymon\JWTAuth\Facades\JWTAuth::fromUser(\Illuminate\Support\Facades\Auth::user()), 'user' => \Illuminate\Support\Facades\Auth::user()]], 200);
});

Route::middleware('jwt.refresh')->get('refresh', function() {

   return response()->json(['data' => [ 'message' => 'Token refreshed'] ])
       ->header('Access-Control-Expose-Headers', 'Authorization');

});

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth'])->get('/admin/{route}', function($route) {

    try {

        return view('sections.admin');

    } catch (InvalidArgumentException $invalidArgumentException) {

        abort(404);

    }
});