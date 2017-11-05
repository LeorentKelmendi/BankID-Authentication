<?php

//Redirect / to login-bankid
Route::get('/', function () {

    return redirect(route('login-bankid'));
});

//Route for login
Route::get('/login-bankid', 'LoginBankIDController@index')->name('login-bankid');
Route::post('/login-bankid', 'LoginBankIDController@login')->name('try-login-bankd');

//Check Status route for jquery request.
Route::get('/check-status', 'LoginBankIDController@checkLogin')->name('check-status-json');

//Logout the user from the app. Flush session
Route::get('/logout', 'LoginBankIDController@logout')->name('logout');

//Added custom middleware to check wether user its authenticated through BankID.

Route::group(['middleware' => 'bankid.auth'], function () {

    Route::get('/profile', 'HomeController@index')->name('profile');

});
