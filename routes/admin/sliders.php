<?php

use App\Http\Controllers\Admin\General\SliderController;
use App\Http\Controllers\Admin\General\SliderItemController;
use Illuminate\Support\Facades\Route;

$controller = SliderController::class;

$master = admin_routes($controller, true, true, true, null, "Quản lý slider", "Cho phép thêm, sửa, xóa các thông tin Slide");

Route::controller($controller)->name('.')->group(function() use($master){

    /**
     * --------------------------------------------------------------------------------------------------------------------
     *                     Method | URI                           | Controller @ Nethod                   | Route Name
     * --------------------------------------------------------------------------------------------------------------------
     */

    $options       = Route::get('options',                        'getSelectOptions'                      )->name('select-options');
    $changeStatus  = Route::post('change-status',                 'changeStatus'                          )->name('change-status');
    $sortForm      = Route::get('sort.html',                      'getSortForm'                           )->name('sort.form');
    $sortSave      = Route::post('sort',                          'sortSliders'                           )->name('sort.save');

    // khai báo module
    $master->addActionByRouter($options, ['view', 'refs', 'create', 'update']);
    $master->addActionByRouter($changeStatus, ['create', 'update']);
    $master->addActionByRouter($sortForm, ['create', 'update']);
    $master->addActionByRouter($sortSave, ['create', 'update']);
});

Route::middleware(['slider.item'])->controller(SliderItemController::class)->name('.items')->group(function () use($master) {
    $items        = Route::get('/{slider}/items.html',           'getIndex'                              )->name("");
    $list         = Route::get('/{slider}/items/list.html',      'getList'                               )->name('.list');
    $sortForm     = Route::get('/{slider}/items/sort.html',      'getSortForm'                           )->name('.sort.form');
    $sortSave     = Route::post('/{slider}/items/sort',          'sortItems'                             )->name('.sort.save');
    $create       = Route::get('/{slider}/items/create.html',    'getCreateForm'                         )->name('.create');
    $update       = Route::get('/{slider}/items/update/{uuid}.html','getUpdateItemForm'                  )->name('.update');
    $save         = Route::post('/{slider}/items/save',          'save'                                  )->name('.save');
    $delete       = Route::post('/{slider}/items/delete',        'delete'                                )->name('.delete');

    // khai báo module
    $sub = $master->addSubByMasterRouter($items, 'Quản lý slider item', 'Cho phép thêm / sửa / xóa / sắp xếp slider item');
    $sub->addActionByRouter($list, ['view']);
    $sub->addActionByRouter($sortForm, ['create', 'update']);
    $sub->addActionByRouter($sortSave, ['create', 'update']);
    $sub->addActionByRouter($create, ['create', 'update']);
    $sub->addActionByRouter($update, ['create', 'update']);
    $sub->addActionByRouter($save, ['view', 'create', 'update']);
    $sub->addActionByRouter($delete, ['delete']);

});
