<?php

use App\Http\Controllers\Admin\General\ThemeController;
use Illuminate\Support\Facades\Route;


$master = admin_routes(
    ThemeController::class, true, true, true, null, "Quản lý giao diện", "Quản lý giao diện và các thành phần hiển thị phía client"
);
Route::controller(ThemeController::class)->name('.')->group(function() use($master){

    /**
     * --------------------------------------------------------------------------------------------------------------------
     *                 Method | URI                             | Controller @ Nethod                   | Route Name
     * --------------------------------------------------------------------------------------------------------------------
     */

    $store       = Route::get('/store.html',                    'getPublishThemes'                      )->name('store');
    $myThemes    = Route::get('/my-themes.html',                'getMyThemes'                           )->name('my-themes');
    $optionForm  = Route::get('options.html',                   'getThemeOptionForm'                    )->name('options');
    $optionGroup = Route::get('options/{group}.html',           'getThemeOptionForm'                    )->name('options.group');
    $optionSave  = Route::post('options/{group}',               'saveThemeOption'                       )->name('options.group.save');
    $themeDetail = Route::get('detail/{id?}',                   'getResourceDetail'                     )->name('detail');
    $myDetail    = Route::get('my-detail/{id?}',                'getMyThemeDetail'                      )->name('my-detail');
    $ajaxSearch  = Route::get('search',                         'ajaxSearch'                            )->name('search');
    $ajaxMyTheme = Route::get('my-themes',                      'ajaxSearchMyThemes'                    )->name('search-my-themes');
    $activeTheme = Route::post('active',                        'active'                                )->name('active');
    $testActive  = Route::get('test-active',                    'active'                                )->name('test-active');
    $download    = Route::get('download/{slug}',                'download'                              )->name('download');
    $extract     = Route::post('extract',                       'extract'                               )->name('extract');
    $dev         = Route::get('dev-update/{id?}',               'devUpdate'                             )->name('dev-update');


    // khai báo module
    $master->addActionByRouter($store, ['update']);
    $master->addActionByRouter($myThemes, ['update', 'create']);
    $master->addActionByRouter($optionForm, ['update']);
    $master->addActionByRouter($optionGroup, ['update']);
    $master->addActionByRouter($optionSave, ['update']);
    $master->addActionByRouter($themeDetail, ['view', 'create', 'update']);
    $master->addActionByRouter($myDetail, ['create', 'update']);
    $master->addActionByRouter($ajaxSearch, ['view', 'create', 'update']);
    $master->addActionByRouter($ajaxMyTheme, ['view', 'create', 'update']);

    $master->addActionByRouter($activeTheme, ['config', 'update']);
    $master->addActionByRouter($testActive, ['config', 'update']);
    $master->addActionByRouter($download, ['view', 'create', 'update']);
    $master->addActionByRouter($extract, ['view', 'create', 'update']);
    $master->addActionByRouter($dev, ['update']);



});
