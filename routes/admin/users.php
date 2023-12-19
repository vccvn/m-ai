<?php

use App\Http\Controllers\Admin\General\AgentController;
use App\Http\Controllers\Admin\General\CIController;
use App\Http\Controllers\Admin\General\UserController;
use Illuminate\Support\Facades\Route;




Route::controller(UserController::class)->group(function(){
    $master = admin_routes(null, true, true, true, null, "Quản lý Người dùng", "Cho phép thêm / sửa / xóa thông tin người dùng");
    /**
     * ----------------------------------------------------------------------------------------------------------------------------
     *                    Method | URI                           | Method                                | Route Name
     * ----------------------------------------------------------------------------------------------------------------------------
     */
    $selectOptions = Route::get('/user-select-options',          'getUserSelectOptions'                  )->name('.select-option');
    $merchantOption= Route::get('/merchant-options',             'getMerchantSelectOptions'              )->name('.merchant-option');
    $tagData       = Route::get('/user-tag-data',                'getUserTagData'                        )->name('.tag-data');
    $status        = Route::post('/change-status',               'changeStatus'                          )->name('.change-status');
    // $updateEncData = Route::any('/update-enc-data',              'updateEncData'                         )->name('.update-enc-data');
    $reset2fa      = Route::post('/reset2fa',                    'reset2fa'                              )->name('.reset2fa');
    $pending       = Route::get('pending',                     [CIController::class,'getCIPendingStatus'])->name('.ci.pending');
    $approve       = Route::post('ci-approve',                 [CIController::class, 'approve']          )->name('.ci.approve');
    $decline       = Route::post('ci-decline',                 [CIController::class, 'decline']          )->name('.ci.decline');
    $delete        = Route::post('ci-delete',                  [CIController::class, 'decline']          )->name('.ci.delete');
    $trash         = Route::post('ci-trash',                   [CIController::class, 'decline']          )->name('.ci.move-to-trash');

    $ciImage       = Route::any('ci-image/{user_id}/{face}',   [CIController::class, 'showCIImage']      )->name('.ci.image');
    // $ciImage       = Route::any('ci-files/{file}',             [CIController::class, 'showCIFile']      )->name('.ci.file');

    $master->addActionByRouter($selectOptions, ['view', 'create', 'update']);
    $master->addActionByRouter($merchantOption, ['view', 'create', 'update']);
    $master->addActionByRouter($tagData, ['view', 'create', 'update']);
    $master->addActionByRouter($status, ['update']);
    $master->addActionByRouter($approve, ['update']);
    $master->addActionByRouter($decline, ['update']);
    $master->addActionByRouter($reset2fa, ['update']);
    $master->addActionByRouter($pending, ['view', 'create', 'update']);
    $master->addActionByRouter($ciImage, ['view']);


});
Route::prefix('agent')->controller(AgentController::class)->name('.agent')->group(function(){
    $master = admin_routes(null, true, true, true, null, "Quản lý Agent", "Cho phép thêm / sửa / xóa thông tin agent");


});
