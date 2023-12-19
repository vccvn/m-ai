<?php

use App\Http\Controllers\Admin\Ecommerce\CustomerController;
use Illuminate\Support\Facades\Route;
Route::controller(CustomerController::class)->group(function(){
    $master = admin_routes(
        null, true, true, 
        true, null, "Quản lý thông tin khách hàng", "Cho phép quản lý thông tin khách mua hàng như họ tên, địa chỉ, liên hệ, vv.. "
    );
    /**
     * -----------------------------------------------------------------------------------------------------------------------------------
     *                   Method | URI                           | Controller @ Nethod                   | Route Name                     |
     * -----------------------------------------------------------------------------------------------------------------------------------
     */

    $opt         = Route::get('/options',                       'getSelectOptions'                      )->name('.select-options');
    $master->addActionByRouter($opt, 'view');
});