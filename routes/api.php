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
    });
});


// ============================================================= DONATION ROUTES
Route::group(['as' => 'donations.'], function () {
    
    Route::group(['prefix' => 'app/donations'], function () {
        Route::post('create'            , 'DonationApiController@create'      )->name('app.create'       );
        Route::post('proof-upload'      , 'DonationApiController@proofUpload' )->name('app.proof.upload' );
        Route::get ('list/{campaignId}' , 'DonationApiController@list'        )->name('app.list'         );
        Route::post('update-status/{donationId}' , 'DonationApiController@updateStatus'    )->name('update.status'      );
        Route::get ('list-by-donator/{id}'       , 'DonationApiController@listByDonator'   )->name('list.by.donator'    );
        Route::get ('list-by-range-date/{id}'    , 'DonationApiController@listByRangeDate' )->name('list.by.range.date' );
    });
    
    Route::group(['prefix' => 'admin/donations'], function () {
        Route::post('create'                        , 'DonationApiController@create'            )->name('app.create'            );
        Route::get ('list-by-donator/{id}'          , 'DonationApiController@listByDonator'     )->name('list.by.donator'       );
        Route::post('update-details/{donationId}'   , 'DonationApiController@updateDetails'     )->name('admin.update.details'  );
        Route::post('update-info/{donationId}'      , 'DonationApiController@updateInfo'        )->name('admin.update.info'     );
    });
});


// ============================================================= CAMPAIGN ROUTES
Route::group(['prefix'=>config('admin.prefix', 'admin'),'middleware' => 'auth:admin'], function () {
    Route::get   ('campaigns'                   , 'CampaignApiController@index'                )->name("campaigns.index"        );
    Route::post  ('campaign'                    , 'CampaignApiController@store'                )->name("campaigns.store"        );
    Route::get   ('campaigns/{id}'              , 'CampaignApiController@show'                 )->name("campaigns.show"         );
    Route::put   ('campaigns/{campaign}'              , 'CampaignApiController@update'               )->name("campaigns.update"       );
    Route::delete('campaigns/{id}'              , 'CampaignApiController@delete'               )->name("campaigns.delete"       );
    
    Route::post  ('campaign/update/finish/{id}' , 'CampaignApiController@updateFinishCampaign' )->name("campaigns.update.finish" );
    Route::put   ('campaigns/{campaign}/toggle/{togglableAttribute}','CampaignApiController@toggle');
    Route::get   ('reports'                     , 'ReportDonationApiController@index'          )->name("report.index"           );
    Route::get   ('reports/{id}'                , 'ReportDonationApiController@show'           )->name("report.show"            );
    Route::get   ('reports/export/excel'        , 'ReportDonationApiController@exportToExcel'  )->name('admin.list.export.excel');
    Route::get   ('reports/export/pdf'          , 'ReportDonationApiController@exportToPdf'    )->name('admin.list.export.pdf'  );
});


Route::get('app/campaigns'     , 'CampaignApiController@index')->name("campaigns.app.index");
Route::get('app/campaigns/{id}', 'CampaignApiController@show' )->name("campaigns.app.show" );