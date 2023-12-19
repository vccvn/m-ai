<?php

use App\Http\Controllers\Admin\General\MenuController;
use App\Http\Controllers\Admin\General\MenuItemController;
use Illuminate\Support\Facades\Route;

$controller = MenuController::class;

$route = 'menus';

$listRoute = ['index', 'create', 'update', 'save', 'delete'];

$master = add_web_module_routes(
    // khai báo route
    $controller, $listRoute, true, 'admin',
    // khai báo module
    true, null, "Quản lý Menu", "Quản lý các menu hiển thị phía trang TMĐT"
);


Route::controller($controller)->name('.')->group(function() use($master){
    
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *             Method | URI                           | Controller @ Nethod                   | Route Name               
     * --------------------------------------------------------------------------------------------------------------------
     */

    $opts  = Route::get('options',                        'getSelectOptions'                      )->name('select-options');
    $stt   = Route::post('change-status',                 'changeStatus'                          )->name('change-status');
    $sortf = Route::get('sort.html',                      'getSortForm'                           )->name('sort.form');
    $sorts = Route::post('sort',                          'sortMenus'                             )->name('sort.save');
    $list  = Route::get('list.html',                      'getMenus'                              )->name('list');
    $master->addActionByRouter($opts, ['refs', 'view']);
    $master->addActionByRouter($stt, ['update', 'view']);
    $master->addActionByRouter($sortf, ['update', 'view']);
    $master->addActionByRouter($sorts, ['update', 'view']);
    $master->addActionByRouter($list, ['update', 'view']);
    

});

Route::middleware(['menu.item'])->controller(MenuItemController::class)->name('.items')->group(function () use($master) {
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *               Method | URI                              | Controller @ Nethod                   | Route Name
     * --------------------------------------------------------------------------------------------------------------------
     */

    $items      = Route::get('/{menu_id}/items.html',          'getItems'                              )->name("");
    $list       = Route::get('/{menu_id}/items/list.html',     'getList'                               )->name('.list');
    $detail     = Route::get('/{menu_id}/items/detail/{id?}',  'getResourceDetail'                     )->name('.detail');
    $sort       = Route::post('/{menu_id}/items/sort',         'sortItems'                             )->name('.sort');
    $save       = Route::post('/{menu_id}/items/save',         'save'                                  )->name('.save');
    $ajaxSave   = Route::post('/{menu_id}/items/ajax-save',    'ajaxSave'                              )->name('.ajax-save');
    $delete     = Route::post('/{menu_id}/items/delete',       'delete'                                )->name('.delete');

    $itemMaster = $master->addSubByMasterRouter($items, "Quản lý Menu Item");
    $itemMaster->addActionByRouter($list, ['view', 'create', 'update']);
    $itemMaster->addActionByRouter($detail, ['view', 'refs']);
    $itemMaster->addActionByRouter($sort, ['view', 'create', 'update', 'refs']);
    $itemMaster->addActionByRouter($save, ['view', 'create', 'update']);
    $itemMaster->addActionByRouter($ajaxSave, ['view', 'create', 'update']);
    $itemMaster->addActionByRouter($delete, ['update']);
});