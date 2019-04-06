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

Route::get('facebook_login',function () {
	return view('login.facebook_login');
});

Route::post('account_kit_login','AccountKitController@login');
Route::get('users','UserController@index');
	