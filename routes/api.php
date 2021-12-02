<?php

use Illuminate\Http\Request;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('insert','TaskController@store');
Route::get('show','TaskController@show');


Route::group(['middleware' => ['signed']] , function() {
    
    Route::any('/price-list' ,'PriceListController@apiGetPriceList')->name('api.price-list');
    Route::get('/tender-categories' , 'Api\MainController@getTenderCategories')->name('api.tender-categories');
    Route::get('/download/price-list' , 'Api\MainController@downloadPricelist')->name('api.download-price-list');
    Route::post('/tenders' , 'Api\MainController@getTenders')->name('api.tender-search');
    Route::get('/tenders/document/{id}' , 'Api\MainController@downloadTender')->name('api.download-tender');
    Route::post('/tenders/items' , 'Api\MainController@getItems')->name('api.tender-items');
    #Route::get('/tenders/document/get' , 'Api\MainController@getItems')->name('api.tender-items');
    
});