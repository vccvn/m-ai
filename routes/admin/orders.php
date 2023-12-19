<?php

use App\Http\Controllers\Admin\Ecommerce\OrderController;
use App\Http\Controllers\Admin\Ecommerce\OrderFeedbackController;
use Illuminate\Support\Facades\Route;

Route::controller(OrderController::class)->group(function(){
    $master = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý đơn hàng", "Cho phép xem, quản lý thông tin, các trạng thái đơn hàng"
    );
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *    Method | URI                           | Controller @ Nethod                   | Route Name                     |
     * --------------------------------------------------------------------------------------------------------------------
     */
    $gpi     = Route::get('/get-product-input',             'getProductInput'                       )->name('.get-product-input');
    $list    = Route::get('/list/{list}.html',              'getListByStatus'                       )->name('.list-by-status');
    $pstt    = Route::post('/change-payment-status',        'changePaymentStatus'                   )->name('.change-payment-status');
    $dstt    = Route::post('/change-delivery-status',       'changeDeliveryStatus'                  )->name('.change-delivery-status');
    $cstt    = Route::post('/change-status',                'changeStatus'                          )->name('.change-status');
    $detail  = Route::get('/detail',                        'getOrderDetail'                        )->name('.resource-detail');
    $options = Route::get('/options',                       'getSelectOptions'                      )->name('.select-options');

    $master->addActionByRouter($gpi, ['view', 'update', 'create']);
    $master->addActionByRouter($list, ['view']);
    $master->addActionByRouter($cstt, ['update', 'create']);
    $master->addActionByRouter($pstt, ['update', 'create', 'refs']);
    $master->addActionByRouter($dstt, ['update', 'create', 'refs']);
    $master->addActionByRouter($detail, ['view']);
    $master->addActionByRouter($options, ['view', 'refs']);
    

});

/*
 *----------------------------------------------------------------------------
 *            Nơi dành cho feedback
 *----------------------------------------------------------------------------
 */

Route::prefix('feedback')->name('.feedback')->controller(OrderFeedbackController::class)->group(function () {
    admin_routes(null, true, true);
    
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *    Method | URI                           | Controller @ Nethod                   | Route Name                     |
     * --------------------------------------------------------------------------------------------------------------------
     */
    Route::get('/detail',                        'getFeedbackDetail'                     )->name('.resource-detail');
    Route::post('/resolve',                      'resolve'                               )->name('.resolve');
    Route::post('/unresolve',                    'unresolve'                             )->name('.unresolve');
});

