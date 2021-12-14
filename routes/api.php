<?php

use Illuminate\Http\Request;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'api','namespace' => 'API'], function(){
    Route::apiResource('/', 'ItemController');  
});

Route::get('/','TaskController@index');
Route::get('show/{id}','TaskController@show');
Route::POST('update/{id}','TaskController@update');
// Route::DELETE('delete/{id}','TaskController@destroy');
Route::post('task','TaskController@store');
Route::delete('delete/{id}','TaskController@destroy');



