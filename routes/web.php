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
    return view('index');
});

// get single order
Route::get('orders/{order_id}', 'OrdersController@getOrder');

// get all orders
Route::get('orders/all', 'OrdersController@getOrders');

// generate print sheet
Route::get('prints/generate', 'PrintsController@generatePrint');

// bind resources
Route::resource('products', 'ProductsController');
Route::resource('orders', 'OrdersController');
Route::resource('prints', 'PrintsController');

