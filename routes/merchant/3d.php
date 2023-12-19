<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Merchant\AR\AngularController;
use App\Http\Controllers\Merchant\AR\Api\ModelController;
use App\Http\Controllers\Merchant\AR\ModelController as ARModelController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->name('.api.')->group(function () {

    $master = ModuleManager::addModuleByRouter( Route::get('/', function () {
        return response(['message' => 'Hello World']);
    })->name('base'), 'Api');

    Route::prefix('items')->controller(ModelController::class)->group(function () use($master) {
        // Route::get('detail/{id}',                      $c.'detail'                               );
        // Route::get('/category',                        'getItemCategories');
        $master->addActionByRouter( Route::get('/list',                            'getData'), ['view']);
        // Route::middleware(['auth.jwt'])->group(function () use($r, $c) {
        $master->addActionByRouter( Route::put('update/{id}',                  'update3D'), ['create', 'update']);
        $master->addActionByRouter( Route::put('thumbnail/{id}',               'updateThumbnail'), ['view', 'create', 'update']);

        $master->addActionByRouter( Route::put('/{id}',                        'update3D'), ['view', 'create', 'update']);
        // });
    });
    Route::prefix('categories')->as('categories')->controller(ModelController::class)->group(function () {
        Route::get('/list',                       'getItemCategories');
    });
});

Route::prefix('models')->controller(ARModelController::class)->name('.models')->group(function () {

    $master = admin_routes(null, true, true, true, null, "Quản lý Model 3D", "Cho phép thêm / sửa / xóa thông tin Model");
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *    Method | URI                           | Controller @ Nethod                   | Route Name                     |
     * --------------------------------------------------------------------------------------------------------------------
     */
    // Route::get('/parent-options',                 $controller.'getParentSelectOptions'   )->name($route.'parent-option');
    // Route::get('/user-tag-data',                  $controller.'getUserTagData'           )->name($route.'tag-data');

    $master->addActionByRouter( Route::get('upload',                          'getUploadForm')->name('.upload'), ['create', 'update']);
    $master->addActionByRouter( Route::post('upload',                         'doUpload'), ['create', 'update']);
    $master->addActionByRouter( Route::post('first-update',                   'FirstUpdate')->name('.first-update'), ['update', 'create']);
    $master->addActionByRouter( Route::get('preview/{secret_id?}',                         'preview')->name('.preview'), ['view']);
    $master->addActionByRouter( Route::get('download-tracking-image/{secret_id?}', 'downloadTrackingImage')->name('.download-tracking-image'), ['view']);
});
Route::prefix('items')->name('.items')->controller(AngularController::class)->group(function () {

    /**
     * --------------------------------------------------------------------------------------------------------------------
     *    Method | URI                           | Controller @ Nethod                   | Route Name                     |
     * --------------------------------------------------------------------------------------------------------------------
     */
    // Route::get('/parent-options',                 $controller.'getParentSelectOptions'   )->name($route.'parent-option');
    // Route::get('/user-tag-data',                  $controller.'getUserTagData'           )->name($route.'tag-data');
    $master = ModuleManager::addModuleByRouter( Route::get('/',                               'nothing')->name('.root'));
    $master->addActionByRouter( Route::get('/category',                       'getItemCategories')->name('.category'), ['view']);
    $master->addActionByRouter( Route::get('/{secret_id}/edit',               'getAngularEditemPage')->name('.edit'), ['create', 'update']);
    // Route::get('/{secret_id}/edit',               'getAngularEditemPage')->name('.create');
});
