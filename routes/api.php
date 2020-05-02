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

Route::middleware('auth:api')->get('/user', function (Request $request) {
	return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
	Route::post('login', 'AuthController@login')->name('login');
	Route::post('signup', 'AuthController@signup');
  
	Route::group(['middleware' => 'auth:api'], function() {
		Route::patch('edit_profile', 'AuthController@updateProfile');
		Route::get('logout', 'AuthController@logout');
	});
});

Route::get('order/{pizza}', 'PizzaController@order')->name('order');
Route::apiResource('pizza', 'PizzaController');

Route::middleware('auth:api')->group(function () {
	// Cart
	Route::get('cart', 'CartController@getCart');
	Route::delete('cart/delete', 'CartController@deleteCart');
	// Items
	Route::group(['prefix' => 'cart'], function () {
		Route::get('increment/item/{item_pos}', 'CartController@incrementItem');
		Route::get('decrement/item/{item_pos}', 'CartController@decrementItem');
		Route::get('remove/item/{item_pos}', 'CartController@removeItem');
	});
});
