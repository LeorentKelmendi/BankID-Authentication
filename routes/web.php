<?php

//Redirect / to login-bankid
Route::get('/', function () {

    return redirect(route('login-bankid'));
});

Route::get('/login-bankid', 'LoginBankIDController@index')->name('login-bankid');
Route::post('/login-bankid', 'LoginBankIDController@login')->name('try-login-bankd');

Auth::routes();
