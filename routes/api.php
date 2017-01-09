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
//
//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:api');

Route::group(['prefix'=>'v1'],function(){

    Route::resource('article','ArticleController',[
        'except' =>['create']
    ]);
    //Route::put('article/{id}', 'ArticleController@update');
    Route::post('user',['uses'=>'AuthController@store']);

    Route::post('user/signin',['uses'=>'AuthController@signin']);
});
