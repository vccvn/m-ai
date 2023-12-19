<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Admin\General\TagController;
use Illuminate\Support\Facades\Route;

// manager_routes($controller, $route, true);
Route::controller(TagController::class)->group(function () {
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *              Method | URI                             | Method                   | Route Name
     * --------------------------------------------------------------------------------------------------------------------
     */

    $index   = Route::get('/',                               'getIndex'                 )->name("");
    $list    = Route::get('/list.html',                      'getList'                  )->name('.list');
    $data    = Route::get('/data.json',                      'getData'                  )->name('.data');
    $create  = Route::post('/create-tags',                   'createTags'               )->name('.create');
    $update  = Route::put('/update',                         'updateTag'                )->name('.update');
    $delete  = Route::post('/delete',                        'delete'                   )->name('.delete');

    // khai báo module
    $master = ModuleManager::addModuleByRouter($index, "Quản lý thẻ", "Cho phép thêm / sửa / xóa thẻ");

    $master->addActionByRouter($list, ['view']);
    $master->addActionByRouter($data, ['view']);
    $master->addActionByRouter($create, ['create']);
    $master->addActionByRouter($update, ['update']);
    $master->addActionByRouter($delete, ['delete']);
});