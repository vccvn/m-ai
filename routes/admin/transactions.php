<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Admin\Ecommerce\OrderTransactionController;
use Illuminate\Support\Facades\Route;

Route::controller(OrderTransactionController::class)->group(function(){
    $master = admin_routes(null, true, true, true, null, "Quản lý giao dịch", "Cho phép xem và phê duyệt các giao dịch thanh toán đơn hàng");
    
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *             Method | URI                           | Controller @ Nethod                   | Route Name             
     * --------------------------------------------------------------------------------------------------------------------
     */
    $detail = Route::get('/detail',                       'getTransactionDetail'                  )->name('.resource-detail');
    $status = Route::post('/status/{slug}',               'changeStatus'                          )->name('.status');

    $master->addActionByRouter($detail, ['view']);
    $master->addActionByRouter($status, ['update']);

});
    


