<?php

use App\Http\Controllers\Admin\General\AgentController;
use App\Http\Controllers\Admin\General\CIController;
use App\Http\Controllers\Admin\General\CoverLetterController;
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
    $selectOptions = Route::get('/user-options',                 'getUserOptions'                        )->name('.user-option');
    $merchantOption= Route::get('/merchant-options',             'getMerchantSelectOptions'              )->name('.merchant-option');
    $tagData       = Route::get('/user-tag-data',                'getUserTagData'                        )->name('.tag-data');
    $status        = Route::post('/change-status',               'changeStatus'                          )->name('.change-status');
    // $updateEncData = Route::any('/update-enc-data',              'updateEncData'                         )->name('.update-enc-data');
    $reset2fa      = Route::post('/reset2fa',                    'reset2fa'                              )->name('.reset2fa');
    $addMonth      = Route::post('/add-month',                   'addMonth'                              )->name('.add-month');


    $master->addActionByRouter($selectOptions, ['view', 'create', 'update']);
    $master->addActionByRouter($merchantOption, ['view', 'create', 'update']);
    $master->addActionByRouter($tagData, ['view', 'create', 'update']);
    $master->addActionByRouter($status, ['update']);
    $master->addActionByRouter($reset2fa, ['update']);


});
Route::prefix('agent')->controller(AgentController::class)->name('.agent')->group(function(){
    $master = admin_routes(null, true, true, true, null, "Quản lý Agent", "Cho phép thêm / sửa / xóa thông tin agent");
});
Route::prefix('cover-letters')->controller(CoverLetterController::class)->name('.cover-letters')->group(function(){
    $master = admin_routes(null, true, true, true, null, "Quản lý Đơn xét duyệt làm đại lý", "Cho phép thêm / sửa / xóa Đơn xét duyệt");
    $status        = Route::post('/change-status',               'changeStatus'                          )->name('.change-status');
    $master->addActionByRouter($status, ['update']);
});
