<?php

use Illuminate\Support\Facades\Route;

Route::get('region-options',               'getRegionOptions'       )->name('regions.options');
Route::get('district-options',             'getDistrictOptions'     )->name('districts.options');
Route::get('ward-options',                 'getWardOptions'         )->name('wards.options');