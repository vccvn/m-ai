<?php

use App\Http\Controllers\Merchant\General\AccountController;
use Illuminate\Support\Facades\Route;


/**
 * --------------------------------------------------------------------------------------------------------------------
 *    Method | URI                           | Controller @ Nethod                   | Route Name                     |
 * --------------------------------------------------------------------------------------------------------------------
 */
Route::controller(AccountController::class)->group(function(){
    Route::get('/',                               'getInfoForm'              )->name('');
    Route::get('/info',                           'getInfoForm'              )->name('.info');
    Route::post('/info',                          'saveInfo');
    Route::get('/security',                       'getSecurityForm'          )->name('.security');
    Route::post('/security',                      'saveSecurity');
    
    
});
