<?php

use App\Http\Controllers\Admin\General\ContactController;
use App\Http\Controllers\Admin\General\ContactReplyController;
use Illuminate\Support\Facades\Route;

$controller = ContactController::class;

$listRoute = ['index', 'list', 'create', 'update', 'save', 'delete'];

$contactModule = add_web_module_routes(
    // khai báo route
    $controller, $listRoute, true, 'admin', 
    // khai báo module 
    true, null, "Quản lý liên hệ", "Xem và phản hồi các thông tin liên hệ của khách hàng"
);
/**
 * -------------------------------------------------------------------------------------------------------------------------------
 *               Method | URI                              | Controller @ Nethod                   | Route Name                  |
 * -------------------------------------------------------------------------------------------------------------------------------
 */

$detail         = Route::get('detail',                     [$controller,'getResourceDetail']       )->name('.detail');
$contactModule->addActionByRouter($detail, 'view');

Route::controller(ContactReplyController::class)->name('.replies')->group(function() use($contactModule) {
    $replies    = Route::get('replies',                   'getAjaxData'                            )->name('');
    $detail     = Route::get('replies/detail',            'getResourceDetail'                      )->name('.detail');
    $save       = Route::post('replies/save',             'ajaxSave'                               )->name('.save');
    $create     = Route::post('replies/create',           'ajaxSave'                               )->name('.create');
    $update     = Route::post('replies/update',           'ajaxSave'                               )->name('.update');
    $delete     = Route::delete('replies/delete',         'delete'                                 )->name('.delete');

    $subModule  = $contactModule->addSub('replies', [
        'name' => 'Phản hồi liên hệ', 
        'description' => 'Xem và phản hồi các liên hệ'
    ]);

    $subModule->addActionByRouter($replies, 'view');
    $subModule->addActionByRouter($detail, 'view');
    $subModule->addActionByRouter($save, 'create');
    $subModule->addActionByRouter($save, 'update');
    $subModule->addActionByRouter($create, 'create');
    $subModule->addActionByRouter($update, 'update');
    $subModule->addActionByRouter($detail, 'delete');
});