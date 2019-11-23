<?php

use App\Http\Controllers\Auth\FacebookController;
use App\Http\Controllers\Auth\TwitterController;

Route::get('/', 'HomeController@index');

Route::get('/auth/twitter',[TwitterController::class,'redirectToProvider']);
Route::get('/auth/twitter/call',[TwitterController::class,'handleProviderCallback']);

Route::get('/auth/facebook',[FacebookController::class,'redirectToProvider']);
Route::get('/auth/facebook/call',[FacebookController::class,'handleProviderCallback']);

Auth::routes();
