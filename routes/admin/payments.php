<?php

use App\Http\Controllers\Admin\Payments\PackageController;
use App\Http\Controllers\Admin\Payments\PaymentMethodController;
use App\Http\Controllers\Admin\Payments\RequestController;
use App\Http\Controllers\Admin\Payments\TransactionController;
use Illuminate\Support\Facades\Route;
Route::prefix('methods')->controller(PaymentMethodController::class)->name('.methods')->group(function () {
    $master = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý phương thức thanh toán", "Cho phép thêm sửa xóa và thiệt lập cấu hình các phương thức thanh toán"
    );
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *               Method | URI                           | Controller @ Nethod                   | Route Name
     * --------------------------------------------------------------------------------------------------------------------
     */



    $inputs = Route::post('/inputs',                        'getMethodInputs'                      )->name('.inputs');
    $ajaxSv = Route::post('/ajax-save',                     'ajaxSave'                             )->name('.ajax.save');
    $updStt = Route::post('/update-status',                 'updateStatus'                         )->name('.ajax.update-status');
    $ajDetail=Route::get('/ajax-detail',                    'getMethodDetail'                      )->name('.ajax.detail');
    $priority=Route::post('/update-priority',               'updatePriority'                       )->name('.ajax.update-priority');

    $master->addActionByRouter($inputs, ['create', 'update']);
    $master->addActionByRouter($ajaxSv, ['create', 'update']);
    $master->addActionByRouter($updStt, ['create', 'update']);
    $master->addActionByRouter($ajDetail, ['create', 'update']);
    $master->addActionByRouter($priority, ['create', 'update']);

});

Route::prefix('packages')->controller(PackageController::class)->name('.packages')->group(function () {
    $master = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý Các gói thanh toánh thanh toán", "Cho phép xem , quản lý các gói thanh toán"
    );
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *               Method | URI                           | Controller @ Method                   | Route Name
     * --------------------------------------------------------------------------------------------------------------------
     */

    $master->addActionByRouter(Route::post('/export',                        'export'                            )->name('.export'), ['view', 'refs']);
    $master->addActionByRouter(Route::get('/download',                        'download'                            )->name('.download'), ['view', 'refs']);


});

Route::prefix('requests')->controller(RequestController::class)->name('.requests')->group(function () {
    $master = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý yêu cầu thanh toán", "Cho phép xem lịch sử yêu cầu thanh toán"
    );
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *               Method | URI                           | Controller @ Method                   | Route Name
     * --------------------------------------------------------------------------------------------------------------------
     */

    $master->addActionByRouter(Route::post('/export',                        'export'                            )->name('.export'), ['view', 'refs']);
    $master->addActionByRouter(Route::get('/download',                        'download'                            )->name('.download'), ['view', 'refs']);


});



Route::prefix('transactions')->controller(TransactionController::class)->name('.transactions')->group(function () {
    $master = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý Giao dịch thanh toán", "Cho phép xem lịch sử thanh toán"
    );
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *               Method | URI                           | Controller @ Method                   | Route Name
     * --------------------------------------------------------------------------------------------------------------------
     */

    $master->addActionByRouter(Route::post('/export',                        'export'                            )->name('.export'), ['view', 'refs']);
    $master->addActionByRouter(Route::get('/download',                        'download'                            )->name('.download'), ['view', 'refs']);


});

