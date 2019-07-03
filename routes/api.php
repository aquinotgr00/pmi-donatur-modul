<?php

// API ROUTES 


Route::get('campaigns', 'CampaignApiController@index')->name("campaigns.index");
Route::post('campaign', 'CampaignApiController@store')->name("campaign.store");
Route::get('campaigns/{id}', 'CampaignApiController@show')->name("campaigns.show");
Route::put('campaigns/{id}', 'CampaignApiController@update')->name("campaigns.update");
Route::delete('campaigns/{id}', 'CampaignApiController@delete')->name("campaigns.delete");