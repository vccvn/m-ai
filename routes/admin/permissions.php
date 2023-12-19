<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Admin\General\PermissionModuleController;
use App\Http\Controllers\Admin\General\PermissionRoleController;
use Illuminate\Support\Facades\Route;
$master = ModuleManager::addModule('admin', 'permissions', [
    'name' => 'Quản lý phân quyền',
    'description' => 'Cho phép cập nhật module, thêm / sửa / xóa và gán quyền cho user',
    'prefix' => 'admin/permissions',
    'path' => 'admin.permissions'
]);
Route::prefix('modules')->name('.modules')->controller(PermissionModuleController::class)->group(function () use($master) {
    
    $sub = admin_routes(null, true, true, true, null, 'Quản lý module', 'Cho phép xem các module chức năng', $master);
    
    /**
     * ---------------------------------------------------------------------------------------------------------------------------------------------
     *                            Method | URI                           | Controller @ Method                   | Route Name                     
     * ---------------------------------------------------------------------------------------------------------------------------------------------
     */
    $sub->addActionByRouter(Route::get('/get-route-options',             'getRouteOptions'                       )->name('.get-route-options'), ['view', 'refs']);

    $sub->addActionByRouter(Route::get('/matrix',                        'showMatrix'                            )->name('.matrix'), ['view', 'refs']);
    $sub->addActionByRouter(Route::get('/map',                           'showMap'                               )->name('.map'), ['view', 'refs']);
    $sub->addActionByRouter(Route::get('/update-from-routes',            'updateFromRoutes'                      )->name('.update-from-routes'), ['update']);
    $sub->addActionByRouter(Route::get('/clear',                         'clear'                                 )->name('.clear'), ['update', 'delete']);
});


Route::prefix('roles')->name('.roles')->controller(PermissionRoleController::class)->group(function () use($master) {
    
    $sub = admin_routes(null, true, true, true, null, 'Quản lý quyền', 'Cho phép thêm / sửa / xóa các quyền và gán cho user hoặc module', $master);

    /**
     * --------------------------------------------------------------------------------------------------------------------
     *    Method | URI                           | Controller @ Method                   | Route Name                     |
     * --------------------------------------------------------------------------------------------------------------------
     */

    $sub->addActionByRouter(Route::post('save-user-role',               'saveUserRole'                           )->name('.save-user-role'), ['update']);
    $sub->addActionByRouter(Route::get('user-tags',                     'getUserTags'                            )->name('.user-tags'), ['update', 'create']);
    

    
});