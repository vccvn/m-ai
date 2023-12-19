<?php

use Illuminate\Support\Facades\Route;



Route::get('/login',                          'getLoginForm'          )->name('login');
Route::post('/post-login',                    'postLogin'             )->name('post-login');
Route::post('/send-email-reset-password',     'sendEmailResetPassword')->name('post-forgot');
Route::get('/logout',                         'logout'                )->name('logout');
Route::get('/reset-password/{token?}',        'getResetPasswordForm'  )->name('password.reset');
Route::post('/save-reset-password',           'postResetPassword'     )->name('password.reset.save');

Route::get('/errors/{code?}',                 'Admin\General\ErrorController@showError'   )->name('errors');
