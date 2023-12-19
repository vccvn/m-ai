<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Admin\Location\LocationController;
use App\Http\Controllers\Admin\Location\PlaceController;
use App\Http\Controllers\Admin\Location\PlaceTypeController;
use Illuminate\Support\Facades\Route;

$master = ModuleManager::addModule('admin', 'location', [
    'name' => 'Quản lý Các Địa điểm, tỉnh thành, khu vựcm ...',
    'description' => 'Cho phép cập nhật module, thêm / sửa / xóa Các Địa điểm, tỉnh thành, khu vựcm ...',
    'prefix' => 'admin/location',
    'path' => 'admin.location'
]);

Route::controller(LocationController::class)->as('.')->group(function() use($master){
    $regionData   = Route::get('region-data',                  'getRegionData'          )->name('regions.data');
    $regionOpts   = Route::get('region-options',               'getRegionOptions'       )->name('regions.options');
    $districtOpts = Route::get('district-options',             'getDistrictOptions'     )->name('districts.options');

    $wardOpts     = Route::get('ward-options',                 'getWardOptions'         )->name('wards.options');

    $master->addActionByRouter($regionData, ['view', 'create', 'update'], "Region Data");
    $master->addActionByRouter($regionOpts, ['view', 'create', 'update'], "Region Option");
    $master->addActionByRouter($districtOpts, ['view', 'create', 'update'], "District Option");
    $master->addActionByRouter($wardOpts, ['view', 'create', 'update'], "Ward Option");

});

// Route::controller(PlaceController::class)->prefix('places')->as('.places')->group(function() use($master){
//     $sub = admin_routes(null, true, true, true, null, 'Quản lý Place', 'Cho phép xem các Place', $master);
//     $districtOpts = Route::get('options',             'getPlacetOptions'     )->name('.options');
//     $ajaxCreate   = Route::post('ajax-create',         'createPlace'     )->name('.ajax-create');
// });
// Route::controller(PlaceTypeController::class)->prefix('place-types')->as('.place-types')->group(function() use($master){
//     $sub = admin_routes(null, true, true, true, null, 'Quản lý Loại Place', 'Cho phép xem xem / chỉnh sửa các loại place', $master);
// });
