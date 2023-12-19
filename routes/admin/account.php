<?php

use App\Http\Controllers\Admin\General\AccountController;
use Illuminate\Support\Facades\Route;


/**
 * --------------------------------------------------------------------------------------------------------------------
 *    Method | URI                           | Controller @ Method                   | Route Name                     |
 * --------------------------------------------------------------------------------------------------------------------
 */
Route::controller(AccountController::class)->group(function(){
    Route::get('/',                               'getInfoForm'              )->name('');
    Route::get('/info',                           'getInfoForm'              )->name('.info');
    Route::post('/info',                          'saveInfo');
    Route::get('/security',                       'getSecurityForm'          )->name('.security');
    Route::post('/security',                      'saveSecurity');
    
    
});
