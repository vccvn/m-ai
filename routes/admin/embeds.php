<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Admin\General\EmbedController;
use Illuminate\Support\Facades\Route;

// manager_routes($controller, $route, true);
Route::controller(EmbedController::class)->group(function(){

    /**
     * ----------------------------------------------------------------------------------------------------------------------------
     *            Method | URI                           | Controller @ Method                   | Route Name                     |
     * ----------------------------------------------------------------------------------------------------------------------------
     */

    
    $master = Route::get('/',                               'showEmbeds'               )->name("");
    
    $list   = Route::get('/list.html',                      'showEmbeds'               )->name('.list');
    $detail = Route::get('/detail/{id?}',                   'getResourceDetail'        )->name('.detail');
    $sort   = Route::post('/sort',                          'sort'                     )->name('.sort');
    $save   = Route::post('/save',                          'save'                     )->name('.save');
    $ajSave = Route::post('ajax-save',                      'ajaxSave'                 )->name('.ajax-save');
    $delete = Route::post('/delete',                        'delete'                   )->name('.delete');
    
    $masterModule = ModuleManager::addModuleByRouter($master, "Quản lý mã nhúng", "Cho phép thêm / sửa / xóa và sắp xếp thứ tự mã nhúng");
    $masterModule->addActionByRouter($list, ['view']);
    $masterModule->addActionByRouter($detail, ['view']);
    $masterModule->addActionByRouter($sort, ['create', 'update']);
    $masterModule->addActionByRouter($save, ['create', 'update']);
    $masterModule->addActionByRouter($ajSave, ['create', 'update']);
    $masterModule->addActionByRouter($delete, ['delete']);

});