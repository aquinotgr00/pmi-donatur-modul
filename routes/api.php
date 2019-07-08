<?php

// ============================================================= DONATOR ROUTES 
Route::group(['prefix' => 'app/donators', 'as' => 'donators.app.'], function () {
    Route::post('signin'          , 'DonatorApiController@signin'         )->name('signin'          );
    Route::post('signup'          , 'DonatorApiController@signup'         )->name('signup'          );
    Route::post('password/reset'  , 'DonatorApiController@resetPassword'  )->name('reset.password'  );
    Route::post('password/change' , 'DonatorApiController@changePassword' )->name('change.password' );
    Route::post('update-profile'  , 'DonatorApiController@updateProfile'  )->name('update.profile'  );
    Route::get ('profile'         , 'DonatorApiController@profile'        )->name('profile'         );
});



// ============================================================= DONATION ROUTES
Route::group(['as' => 'donations.'], function () {
    Route::group(['prefix' => 'app/donations'], function () {
        Route::post('create'      , 'DonationApiController@create'     )->name('app.create'      );
        Route::post('proof-upload', 'DonationApiController@proofUpload')->name('app.proof.upload');
    });
    Route::group(['prefix' => 'admin/donations', 'middleware' => 'auth:admin'], function () {
        Route::post('create'      , 'DonationApiController@create'     )->name('admin.create'    );
    });
});



// ============================================================= CAMPAIGN ROUTES
Route::group(['prefix'=>config('admin.prefix', 'admin'),'middleware' => 'auth:admin'], function () {
    Route::get   ('campaigns'                   , 'CampaignApiController@index'                )->name("campaigns.index"        );
    Route::post  ('campaign'                    , 'CampaignApiController@store'                )->name("campaigns.store"         );
    Route::get   ('campaigns/{id}'              , 'CampaignApiController@show'                 )->name("campaigns.show"         );
    Route::put   ('campaigns/{id}'              , 'CampaignApiController@update'               )->name("campaigns.update"       );
    Route::delete('campaigns/{id}'              , 'CampaignApiController@delete'               )->name("campaigns.delete"       );
    Route::post  ('campaign/update/finish/{id}' , 'CampaignApiController@updateFinishCampaign' )->name("campaigns.update.finish" );
});



Route::get('app/campaigns'     , 'CampaignApiController@index')->name("campaigns.app.index");
Route::get('app/campaigns/{id}', 'CampaignApiController@show' )->name("campaigns.app.show" );