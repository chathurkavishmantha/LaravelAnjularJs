<?php

Route::get('/tenders/tender-items/get/{Tender}' , 'TenderController@getTenderItems');
Route::post('/tenders/tender-items/remove' , 'TenderController@removeTenderItem');
Route::post('/tenders/tender-items/add' , 'TenderController@addTenderItem');
Route::post('/tenders/tender-items/search' , 'TenderController@searchTenderItem')->name('item-search');

Route::get('/tenders/download/{tender}' ,'TenderController@download')->name('download-tender');
Route::post('/tenders/upload-file' ,'TenderController@uploadFile');

Route::resource('/tenders' , 'TenderController');