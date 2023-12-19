<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Admin\General\SettingController;
use App\Http\Controllers\Admin\General\UrlSettingController;
use App\Http\Controllers\Admin\General\WebConfigController;
use Illuminate\Support\Facades\Route;



$master = ModuleManager::addModule('admin', 'settings', [
    'name' => 'Thiết lập',
    'type' => 'module',
    'prefix' => 'admin/settings',
    'path' => 'admin.settings'
]);


Route::prefix('urls')->controller(UrlSettingController::class)->name('.urls')->group(function(){
    // manager_routes($s, $route, true);

    /**
     * --------------------------------------------------------------------------------------------------------------------
     *    Method | URI                           | Controller @ Method                   | Route Name                     |
     * --------------------------------------------------------------------------------------------------------------------
     */

    Route::get('/',                               'getUrlSettingForm'                     )->name("");
    Route::get('/{group}.html',                   'getUrlSettingForm'                     )->name('.group');
    Route::post('/{group}/save',                  'saveSettings'                          )->name('.group.save');
    

});






// manager_routes($s, $route, true);

Route::controller(WebConfigController::class)->group(function() use($master){

    /**
     * --------------------------------------------------------------------------------------------------------------------
     *    Method | URI                           | Controller @ Method                   | Route Name                     |
     * --------------------------------------------------------------------------------------------------------------------
     */

    $master->addActionByRouter(Route::get('/webconfig.html','getWebConfigForm')->name('.webconfig'), ['update', 'view'], 'Cập nhật cấu hình');
    $master->addActionByRouter(Route::post('/webconfig/save', 'handle')->name('.webconfig.save'), ['update'], 'Lưu cập nhật');
    
});

Route::controller(SettingController::class)->name('.')->group(function() use($master){
    $update = Route::post('/update',                        'handle'                               )->name('handle');

    $form   = Route::get('/{group}.html',                   'getSettingForm'                       )->name('group.form');
    $save   = Route::post('/{group}/save',                  'handle'                               )->name('group.save');
    $item   = Route::post('/{group}/items/save',            'saveSettingItem'                      )->name('item.save');
    
    $detail = Route::get('{group}/detail-item/{id?}',       'detailItem'                           )->name('item.detail');
    $sortF  = Route::get('{group}/sort.html',               'getSortForm'                          )->name('sort.form');
    $sortS  = Route::post('{group}/sort',                   'sortItems'                            )->name('sort.save');
    $delete = Route::post('{group}/delete',                 'deleteItem'                           )->name('item.delete');

    $master->addActionByRouter($update, ['update'], "Cập nhật", null);
    $master->addActionByRouter($form, ['update', 'view'], "Form Cập nhật", null);
    $master->addActionByRouter($save, ['update'], "Cập nhật", null);
    $master->addActionByRouter($item, ['update'], "Save Item", null);
    $master->addActionByRouter($detail, ['update', 'view'], null, "Chi tiết");
    $master->addActionByRouter($sortF, ['update']);
    $master->addActionByRouter($sortS, ['update'], "Save Item", null);
    $master->addActionByRouter($delete, ['update'], "delete", null);
    
    
});
