<?php

// ============================================================= DONATOR ROUTES 
Route::group(['as' => 'donators.'], function () {
    Route::group(['prefix' => 'app', 'as' => 'app.'], function () {
        Route::post('signin'          , 'DonatorApiController@signin'         )->name('signin'          );
        Route::post('signup'          , 'DonatorApiController@signup'         )->name('signup'          );
        Route::post('password/reset'  , 'DonatorApiController@resetPassword'  )->name('reset.password'  );
        Route::post('password/change' , 'DonatorApiController@changePassword' )->name('change.password' );
        Route::post('update-profile'  , 'DonatorApiController@updateProfile'  )->name('update.profile'  );
        Route::get ('profile'         , 'DonatorApiController@profile'        )->name('profile'         );
    });
    Route::group(['prefix' => 'admin/donators', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
        Route::get('/', 'DonatorApiController@list')->name('list');
        Route::get('show/{id}', 'DonatorApiController@show')->name('show');
        Route::post('update/{id}'  , 'DonatorApiController@update'  )->name('update'  );
    });
});


// ============================================================= DONATION ROUTES
Route::group(['as' => 'donations.'], function () {
    
    Route::group(['prefix' => 'app/donations'], function () {
        Route::post('/', 'DonationApiController@store')->name('app.create');
        Route::post('proof-upload'              , 'DonationApiController@proofUpload' )->name('app.proof.upload' );
        Route::get ('list/{campaignId}'         , 'DonationApiController@index'        )->name('app.list'         );
        Route::post('update-status/{donation}'  , 'DonationApiController@update'    )->name('update.status'      );
        Route::get ('list-by-donator/{id}'      , 'DonationApiController@listByDonator'   )->name('list.by.donator'    );
        Route::get ('list-by-range-date/{id}'   , 'DonationApiController@listByRangeDate' )->name('list.by.range.date' );
    });
    
    Route::group(['prefix' => 'admin/donations'], function () {
        Route::post('/', 'DonationApiController@store')->name('app.create');
        Route::get ('list-by-donator/{id}'          , 'DonationApiController@listByDonator'     )->name('list.by.donator');
        Route::post('update/{donation}'             , 'DonationApiController@update'            )->name('admin.update'   );
        Route::get ('list/{campaignId}'             , 'DonationApiController@index'             )->name('admin.list.campaignId' );            
    });
});


// ============================================================= CAMPAIGN ROUTES
Route::group(['prefix'=>config('admin.prefix', 'admin'),'middleware' => 'auth:admin'], function () {
    Route::apiResource('campaigns', 'CampaignApiController');
    
    Route::post  ('campaigns/update/finish/{campaign}' , 'CampaignApiController@updateFinishCampaign' )->name("campaigns.update.finish" );
    Route::put   ('campaigns/{campaign}/toggle/{togglableAttribute}','CampaignApiController@toggle');
    Route::get   ('reports'                     , 'ReportDonationApiController@index'          )->name("report.index"           );
    Route::get   ('reports/{id}'                , 'ReportDonationApiController@show'           )->name("report.show"            );
    Route::get   ('reports/export/excel'        , 'ReportDonationApiController@exportToExcel'  )->name('admin.list.export.excel');
    Route::get   ('reports/export/pdf'          , 'ReportDonationApiController@exportToPdf'    )->name('admin.list.export.pdf'  );
    Route::get   ('reports/export/print'        , 'ReportDonationApiController@exportToPrint'  )->name('admin.list.export.print');

});


Route::group(['prefix' => 'app/campaigns', 'as' => 'campaigns.app.'], function () {
    Route::get('/'     , 'CampaignApiController@index')->name('index');
    Route::get('/{campaign}', 'CampaignApiController@show' )->name('show');
    Route::get('/{campaign}/donators', 'CampaignApiController@donatorList')->name('donator-list');
});