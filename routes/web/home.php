<?php

use Illuminate\Support\Facades\Route;
/*
 |---------------------------------------------------------------------------------------------
 |                                         Route Table
 |---------------------------------------------------------------------------------------------
 | METHOD | URI                           | ACTIOM                        | NAME
 |---------------------------------------------------------------------------------------------
 */


Route::get('/',                          'getIndexPage')->name('home');
Route::get('/home',                      'getIndexPage');
Route::get('/index.html',                'getIndexPage')->name('web.home');
Route::get('/Default.aspx',              'getIndexPage')->name('web.default');
Route::get('csrf-token',                 'getCSRFToken')->name('web.token');
Route::get('/crawldata',                 'crawlData');
