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


Route::group(['middleware' => ['cors', 'json.response']], function(){
    Route::post('/register', "App\Http\Controllers\AuthController@register");
    Route::post('/login', "App\Http\Controllers\AuthController@login");
});

Route::middleware('auth:api')->group(function () {
    Route::get('/get-all', 'App\Http\Controllers\NewsController@getAll');
    Route::post('/get-detail', 'App\Http\Controllers\NewsController@getDetail');
    Route::post('/create', 'App\Http\Controllers\NewsController@create');
    Route::post('/delete', 'App\Http\Controllers\NewsController@delete');
    Route::post('/update', 'App\Http\Controllers\NewsController@update');
    Route::get('/image/{fileName}', 'App\Http\Controllers\NewsController@getImage');
//     Route::get('/image/{filename}', function ($filename)
// {
//     return Image::make(storage_path('public/' . $filename))->response();
// });
});