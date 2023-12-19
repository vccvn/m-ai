<?php

use App\Http\Controllers\Merchant\Payments\PackageController;
use App\Http\Controllers\Merchant\Payments\PaymentMethodController;
use App\Http\Controllers\Merchant\Payments\RequestController;
use App\Http\Controllers\Merchant\Payments\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('requests')->controller(RequestController::class)->name('.requests')->group(function () {
    $master = merchant_routes(
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
    $master = merchant_routes(
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

