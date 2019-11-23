<?php

use App\Http\Controllers\AudioController;
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

Route::get('/audio',[AudioController::class,'index']);
Route::post('/audio',[AudioController::class,'upload']);
Route::delete('/audio/{id}',[AudioController::class,'delete']);
