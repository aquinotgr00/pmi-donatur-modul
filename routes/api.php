<?php

// DONATOR ROUTES 
Route::group(['prefix' => 'donators', 'as' => 'auth.donators.'], function () {
    Route::post('signin'             , 'DonatorApiController@signin'                   )->name("signin"               );
    Route::post('signup'             , 'DonatorApiController@signup'                   )->name("signup"               );
    Route::post('password/reset'     , 'DonatorApiController@createTokenForgotPassword')->name("token.password.reset" );
    Route::post('password/change'    , 'DonatorApiController@changePassword'           )->name("token.password.change");
    Route::post('update-profile/{id}', 'DonatorApiController@updateDonatorProfile'     )->name('update.profile'       );
});

// DONATION ROUTES
Route::group(['prefix' => 'donations', 'as' => 'donations.'], function () {
    Route::post('create', 'DonationApiController@create')->name('create');
    Route::post('proof-upload', 'DonationApiController@proofUpload')->name('proof-upload');
});

Route::group(['prefix'=>config('admin.prefix', 'admin'),'middleware' => 'auth:admin'], function () {
    Route::get('campaigns', 'CampaignApiController@index')->name("campaigns.index");
    Route::post  ('campaign'                   , 'CampaignApiController@store'               )->name("campaign.store"        );
    Route::get   ('campaigns/{id}'             , 'CampaignApiController@show'                )->name("campaigns.show"        );
    Route::put   ('campaigns/{id}'             , 'CampaignApiController@update'              )->name("campaigns.update"      );
    Route::delete('campaigns/{id}'             , 'CampaignApiController@delete'              )->name("campaigns.delete"      );
    Route::post  ('campaign/update/finish/{id}', 'CampaignApiController@updateFinishCampaign')->name("campaign.update.finish");
});

Route::get('app/campaigns', 'CampaignApiController@index')->name("campaigns.app.index");
Route::get('app/campaigns/{id}', 'CampaignApiController@show')->name("campaigns.app.show");