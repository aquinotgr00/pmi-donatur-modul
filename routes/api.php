<?php

// API ROUTES 
Route::post('signin', 'DonatorController@signin')->name("auth.donator.signin");
Route::post('signup', 'DonatorController@signup')->name("auth.donator.signup");
Route::post('token/verify', 'DonatorController@verifyDonatorAccessToken')->name("auth.donator.token.verify");
Route::post('token/signin', 'DonatorController@tokenMemberVerification')->name("auth.donator.token.signin");
Route::post('password/reset', 'DonatorController@createTokenForgotPassword')->name("auth.donator.token.password.reset");
Route::post('password/change', 'DonatorController@changePassword')->name("auth.donator.token.password.change");