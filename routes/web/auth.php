<?php

use App\Http\Controllers\Web\Account\AuthController;
use App\Http\Controllers\Web\Account\CheckAuthController;
use Illuminate\Support\Facades\Route;

Route::name('auth.')->controller(AuthController::class)->group(function(){
/**
 * -------------------------------------------------------------------------------------------------------------------------------
 *  Method | URI                             | Controller @ Nethod               | Route Name
 * -------------------------------------------------------------------------------------------------------------------------------
 */

Route::get('dang-nhap.html',                  'getLoginForm'                     )->name('login');
Route::post('dang-nhap.html',                 'postLogin'                        );
Route::post('dang-nhap',                      'postLogin'                        )->name('post-login');
Route::get('quen-mat-khau.html',              'getForgotForm'                    )->name('forgot');
Route::post('quen-mat-khau.html',             'sendEmailResetPassword'           );
Route::post('quen-mat-khau',                  'sendEmailResetPassword'           )->name('post-forgot');

Route::get('cfp-token/{token?}',              'confirmPasswordToken'             )->name('password.confirm-token');
Route::get('dat-lai-mat-khau.html',           'getResetPasswordForm'             )->name('password.reset');
Route::post('dat-lai-mat-khau.html',          'postResetPassword'                );

Route::get('dang-ky.html',                    'getRegisterForm'                  )->name('register');
Route::post('dang-ky.html',                   'postRegister'                     );
Route::post('dang-ky',                        'postRegister'                     )->name('post-register');

Route::get('xac-minh-tai-khoan.html',         'getVerifiForm'                    )->name('verify.form');
Route::post('xac-minh-tai-khoan.html',        'SendVerifyEmail'                  );
Route::post('gui-email-xac-minh',             'SendVerifyEmail'                  )->name('verify.send-email');
Route::get('verify-email/{token?}',           'verifyEmail'                      )->name('verify-email');
Route::any('dang-xuat',                       'logout'                           )->name('logout');


});
Route::post('ajax/check-auth',                     [CheckAuthController::class, 'check']         )->name('auth.check');
