<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Admin\General\ComponentController;
use Illuminate\Support\Facades\Route;

// manager_routes($controller, $route, true);
Route::controller(ComponentController::class)->group(function(){

    /**
     * --------------------------------------------------------------------------------------------------------------------
     *              Method | URI                             | Controller @ Nethod                   | Route Name  
     * --------------------------------------------------------------------------------------------------------------------
     */

    $master =  Route::get('/',                               'showComponents'                        )->name('');
    

    $list   = Route::get('/list.html',                      'showComponents'                        )->name('.list');
    $detail = Route::get('/detail/{id?}',                   'getComponentDetail'                    )->name('.detail');
    $sort   = Route::post('/sort',                          'sort'                                  )->name('.sort');
    $save   = Route::post('/save',                          'save'                                  )->name('.save');
    $ajSave = Route::post('ajax-save',                      'ajaxSave'                              )->name('.ajax-save');
    $delete = Route::post('/delete',                        'delete'                                )->name('.delete');
    $input  = Route::match(['get', 'post'], '/inputs',      'getComponentInputs'                    )->name('.inputs');
    
    $master = ModuleManager::addModuleByRouter($master, 'Quản lý thành phần hiển thị', "Cho phép thêm / sửa / xóa các thành phần sẽ hiển thị ngoài trang client");

    $master->addActionByRouter($list, ['view']);
    $master->addActionByRouter($detail, ['view']);
    $master->addActionByRouter($input, ['view']);
    
    $master->addActionByRouter($sort, ['update']);
    $master->addActionByRouter($save, ['update']);
    $master->addActionByRouter($ajSave, ['update']);
    $master->addActionByRouter($input, ['update']);

    $master->addActionByRouter($delete, ['delete']);


    
});
