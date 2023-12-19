<?php

use App\Http\Controllers\Merchant\Bookings\BookingController;
use App\Http\Controllers\Admin\Requires\RequireController;
use Illuminate\Support\Facades\Route;




Route::controller(BookingController::class)->group(function(){
    $master = merchant_routes(null, true, true, true, null, "Quản lý Booking", "Cho phép thêm / sửa / xóa thông tin Booking");
    /**
     * ----------------------------------------------------------------------------------------------------------------------------
     *                    Method | URI                           | Method                                | Route Name
     * ----------------------------------------------------------------------------------------------------------------------------
     */
    $status        = Route::post('/change-status',               'changeStatus'                          )->name('.change-status');
    $idle          = Route::get('/idle',                         'getIdleList'                           )->name('.idle');
    $confirmed     = Route::get('/confirmed',                    'getConfirmedList'                      )->name('.confirmed');
    $completed     = Route::get('/completed',                    'getCompletedList'                      )->name('.completed');
    $canceled      = Route::get('/canceled',                     'getCanceledList'                       )->name('.canceled');
    $master->addActionByRouter($status, ['update']);
    $master->addActionByRouter($idle, ['view']);
    $master->addActionByRouter($confirmed, ['view']);
    $master->addActionByRouter($completed, ['view']);
    $master->addActionByRouter($canceled, ['view']);


});
