<?php

Route::post('charge', 'MidtransController@charge');

Route::group(['prefix'=>'paygate'], function () {
    
    Route::group(['prefix' => 'payment'], function () {
    	Route::post('/finish'	, 'MidtransController@finish')->name('payment.finish');
    	Route::get('/unfinish'	, 'MidtransController@unfinish')->name('payment.unfinish');
    	Route::get('/error'		, 'MidtransController@error')->name('payment.error');
    });

    Route::post('/notification'	, 'MidtransController@notification')->name('notification');

});