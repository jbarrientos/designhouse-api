<?php

// public routes
Route::get('me', 'User\MeController@getMe');


// authenticated users
Route::group(['middleware' => ['auth:api']], function(){

    Route::post('logout', 'Auth\LoginController@logout');

    Route::put('settings/profile', 'User\SettingsController@updateProfile');
    Route::put('settings/password','User\SettingsController@updatePassword');

    // Upload designs
    Route::post('designs', 'Designs\UploadController@upload');

});


Route::group(['middleware' => ['guest:api']], function(){
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend')->name('verification.resend');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::post('login', 'Auth\LoginController@login')->name('login');


});
