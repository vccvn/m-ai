<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\Account\AccountController;
use Illuminate\Support\Facades\Route;



// Route::get('/login',                          'getLoginForm'          )->name('login');
// Route::post('/post-login',                    'postLogin'             )->name('post-login');
// Route::post('/send-email-reset-password',     'sendEmailResetPassword')->name('post-forgot');

Route::controller(AuthController::class)->group(function(){

    Route::get('/logout',                         'logout'                )->name('logout');
    Route::get('/reset-password/{token?}',        'getResetPasswordForm'  )->name('password.reset');
    Route::post('/save-reset-password',           'postResetPassword'     )->name('password.reset.save');
});

Route::controller(AccountController::class)->group(function(){
    Route::get('/login', 'getSignInForm')->name('login');


    Route::as('account.')->group(function(){

        Route::get('sign-up', 'getSignUpForm')->name('sign-up');
        Route::post('post-signup','postSignUp')->name('signup');


        Route::any('csrf-token','getCSRFToken')->name('token');
        Route::get('sign-in','getSignInForm')->name('sign-in');
        Route::post('post-sign-in', 'postSignIn')->name('signin');


        Route::get('verify','verifyEmail')->name('verify-email');
        Route::get('account/alert','showAlertMessage')->name('alert');
    });
});
