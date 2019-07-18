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
    Route::group(['prefix' => 'admin/donators', 'as' => 'admin.'], function () {
        Route::get('/', 'DonatorApiController@list')->name('list');
    });
});


// ============================================================= DONATION ROUTES
Route::group(['as' => 'donations.'], function () {
    Route::group(['prefix' => 'app/donations'], function () {
        Route::post('create'            , 'DonationApiController@create'      )->name('app.create'       );
        Route::post('proof-upload'      , 'DonationApiController@proofUpload' )->name('app.proof.upload' );
        Route::get ('list/{campaignId}' , 'DonationApiController@list'        )->name('app.list'         );
    });
    Route::group(['prefix' => 'admin/donations', 'middleware' => 'auth:admin'], function () {
        Route::post('create'                     , 'DonationApiController@create'       )->name('admin.create'        );
        Route::get ('list/{campaignId}'          , 'DonationApiController@list'         )->name('admin.list'          );
        Route::post('update-status/{donationId}' , 'DonationApiController@updateStatus' )->name('admin.update.status' );
    });
});


// ============================================================= CAMPAIGN ROUTES
Route::group(['prefix'=>config('admin.prefix', 'admin'),'middleware' => 'auth:admin'], function () {
    Route::get   ('campaigns'                   , 'CampaignApiController@index'                )->name("campaigns.index"        );
    Route::post  ('campaign'                    , 'CampaignApiController@store'                )->name("campaigns.store"        );
    Route::get   ('campaigns/{id}'              , 'CampaignApiController@show'                 )->name("campaigns.show"         );
    Route::put   ('campaigns/{id}'              , 'CampaignApiController@update'               )->name("campaigns.update"       );
    Route::delete('campaigns/{id}'              , 'CampaignApiController@delete'               )->name("campaigns.delete"       );
    
    Route::post  ('campaign/update/finish/{id}' , 'CampaignApiController@updateFinishCampaign' )->name("campaigns.update.finish" );
    Route::put   ('campaigns/{campaign}/toggle/{togglableAttribute}','CampaignApiController@toggle');
    Route::get   ('reports'                     , 'ReportDonationApiController@index'          )->name("report.index"           );
    Route::get   ('reports/{id}'                , 'ReportDonationApiController@show'           )->name("report.show"            );
});


Route::get('app/campaigns'     , 'CampaignApiController@index')->name("campaigns.app.index");
Route::get('app/campaigns/{id}', 'CampaignApiController@show' )->name("campaigns.app.show" );