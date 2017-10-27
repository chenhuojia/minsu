<?php
Route::post('avc','OrderController@info');
Route::any('avi','Api\OrderController@addOrder');
Route::any('test','Api\AuthController@index');