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
Route::any('sendsms','Api\OrderController@sendsms');
Route::any('postage/{id}','Api\PostageController@add');
Route::any('index','Admin\Index@index');
Route::any('welcome','Admin\Index@welcome');
Route::any('order','Admin\Index@order');
Route::any('view/{id}','Admin\Index@view');
Route::any('ewe','Admin\Postage@ewe');
Route::any('other','Admin\Postage@other');
Route::any('addewe','Admin\Postage@addEwe');
Route::any('addother','Admin\Postage@addOther');
Route::any('getorder','Admin\Index@ges');
Route::any('getparma','Admin\PostageList@bindedList');