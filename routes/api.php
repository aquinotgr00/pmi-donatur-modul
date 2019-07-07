<?php

// API DONATOR ROUTES 
Route::group(['prefix' => 'donators'], function () {
    Route::post('signin', 'DonatorController@signin')->name("auth.donator.signin");
    Route::post('signup', 'DonatorController@signup')->name("auth.donator.signup");
    Route::post('password/reset', 'DonatorController@createTokenForgotPassword')->name("auth.donator.token.password.reset");
    Route::post('password/change', 'DonatorController@changePassword')->name("auth.donator.token.password.change");
    Route::post('update-profile/{id}', 'DonatorController@updateDonatorProfile')->name('auth.donator.update.profile');
});

Route::group(['prefix'=>config('admin.prefix', 'admin'),'middleware' => 'auth:admin'], function () {
    Route::get('campaigns/all/filter', 'CampaignApiController@allFilter')->name("campaigns.all.filter");
    Route::post('campaign', 'CampaignApiController@store')->name("campaign.store");
    Route::get('campaigns/{id}', 'CampaignApiController@show')->name("campaigns.show");
    Route::put('campaigns/{id}', 'CampaignApiController@update')->name("campaigns.update");
    Route::delete('campaigns/{id}', 'CampaignApiController@delete')->name("campaigns.delete");
    Route::post('campaign/update/finish/{id}', 'CampaignApiController@updateFinishCampaign')->name("campaign.update.finish");
});

//published campaigns
Route::get('campaigns', 'CampaignApiController@index')->name("campaigns.index");