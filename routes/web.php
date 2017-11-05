<?php

//Redirect / to login-bankid
Route::get('/', function () {

    return redirect(route('login-bankid'));
});

Route::get('/login-bankid', 'LoginBankIDController@index')->name('login-bankid');
Route::post('/login-bankid', 'LoginBankIDController@login')->name('try-login-bankd');

Route::get('/check-status', 'LoginBankIDController@checkLogin')->name('check-status-json');
Route::get('/logout', 'LoginBankIDController@logout')->name('logout');

Route::group(['middleware' => 'bankid.auth'], function () {

    Route::get('/profile', 'HomeController@index')->name('profile');

});
