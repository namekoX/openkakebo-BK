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

Route::middleware('auth:api')->get('user', function (Request $request) {
    return $request->user();
});

Route::options("login", function () {});
Route::post('login', 'Auth\LoginController@login')->name('login.login');

Route::options("getuser", function () {});
Route::get("getuser", 'Auth\LoginController@getuser')->name('login.getuser');

Route::options("password", function () {});
Route::put("password", 'Auth\LoginController@password')->name('login.password');

Route::options("mail", function () {});
Route::put("mail", 'Auth\LoginController@mail')->name('login.mail');

Route::options("createuser", function () {});
Route::post("createuser", 'Auth\LoginController@store')->name('login.store');

Route::options("logout", function () {});
Route::post("logout", 'Auth\LoginController@logout')->name('login.logout');

Route::options("category", function () {});
Route::get('category', 'CategoryController@index')->name('category.index');
Route::put('category', 'CategoryController@update')->name('category.update');

Route::options("koza", function () {});
Route::get('koza', 'KozaController@index')->name('koza.index');
Route::put('koza', 'KozaController@update')->name('koza.update');

Route::options("kokaiconfig", function () {});
Route::get('kokaiconfig', 'KokaiConfigController@index')->name('kokaiconfig.index');
Route::put('kokaiconfig', 'KokaiConfigController@update')->name('kokaiconfig.update');

Route::options("shushi", function () {});
Route::get('shushi', 'ShushiController@index')->name('shushi.index');
Route::post('shushi', 'ShushiController@store')->name('shushi.store');
Route::put('shushi', 'ShushiController@update')->name('shushi.update');
Route::delete('shushi', 'ShushiController@delete')->name('shushi.delete');

Route::options("summary", function () {});
Route::get('summary', 'SummaryController@index')->name('summary.index');
Route::options("public", function () {});
Route::get('public', 'SummaryController@public')->name('summary.public');

function getUser()
{
    $req = request();
    $token = request()->bearerToken();
    $user = \App\User::where("token", $token)->first();
    if ($token && $user) {
        return $user;
    } else {
        return null;        
    }
};
