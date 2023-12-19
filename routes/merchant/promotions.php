<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Merchant\Promotions\CampaignController;
use App\Http\Controllers\Merchant\Promotions\VoucherController;
use Illuminate\Support\Facades\Route;
$master = ModuleManager::addModule('merchant', 'promotions', [
    'name' => 'Quản lý Các chương trình khuyến mãi',
    'description' => 'Cho phép cập nhật module, thêm / sửa / xóa Các chương trình khuyến mãi',
    'prefix' => 'merchant/promotions',
    'path' => 'merchant.promotions'
]);
Route::prefix('campaigns')->name('.campaigns')->controller(CampaignController::class)->group(function () use($master) {

    $sub = merchant_routes(null, true, true, true, null, 'Quản lý Chiến dịch khuyến mãi', 'Cho phép xem các Chiến dịch quảng cáo', $master);

    /**
     * ---------------------------------------------------------------------------------------------------------------------------------------------
     *                            Method | URI                           | Controller @ Method                   | Route Name
     * ---------------------------------------------------------------------------------------------------------------------------------------------
     */
    $sub->addActionByRouter(Route::post('/run',                           'runCampaign'                          )->name('.run'), ['create', 'update']);
    $sub->addActionByRouter(Route::post('/stop',                          'stopCampaign'                         )->name('.stop'), ['create', 'update']);

    // $sub->addActionByRouter(Route::get('/matrix',                        'showMatrix'                            )->name('.matrix'), ['view', 'refs']);
    // $sub->addActionByRouter(Route::get('/map',                           'showMap'                               )->name('.map'), ['view', 'refs']);
    // $sub->addActionByRouter(Route::get('/update-from-routes',            'updateFromRoutes'                      )->name('.update-from-routes'), ['update']);
    // $sub->addActionByRouter(Route::get('/clear',                         'clear'                                 )->name('.clear'), ['update', 'delete']);
});

Route::prefix('vouchers')->name('.vouchers')->controller(VoucherController::class)->group(function () use($master) {

    $sub = merchant_routes(null, true, true, true, null, 'Quản lý Voucher', 'Cho phép quản lý các voucher', $master);

    /**
     * ---------------------------------------------------------------------------------------------------------------------------------------------
     *                            Method | URI                           | Controller @ Method                   | Route Name
     * ---------------------------------------------------------------------------------------------------------------------------------------------
     */
    // $sub->addActionByRouter(Route::get('/get-route-options',             'getRouteOptions'                       )->name('.get-route-options'), ['view', 'refs']);

    $sub->addActionByRouter(Route::get('/all',                           'allVoucher'                             )->name('.all'), ['view', 'refs']);
    // $sub->addActionByRouter(Route::get('/matrix',                        'showMatrix'                            )->name('.matrix'), ['view', 'refs']);
    // $sub->addActionByRouter(Route::get('/map',                           'showMap'                               )->name('.map'), ['view', 'refs']);
    // $sub->addActionByRouter(Route::get('/update-from-routes',            'updateFromRoutes'                      )->name('.update-from-routes'), ['update']);
    // $sub->addActionByRouter(Route::get('/clear',                         'clear'                                 )->name('.clear'), ['update', 'delete']);
});
