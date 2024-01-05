<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Admin\Affiliate\CommissionPolicyController;
use Illuminate\Support\Facades\Route;



$master = ModuleManager::addModule('admin', 'policies', [
    'name' => 'Quản lý các chính sách',
    'description' => 'Cho phép xem sửa xoá các chính sách',
    'prefix' => 'admin/policies',
    'path' => 'admin.policies'
]);



Route::prefix('commissions')->name('.commissions')->controller(CommissionPolicyController::class)->group(function() use ($master){

    $CateMaster = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý Chính sách hoa hồng", "Cho phép thêm / sửa / xóa các Chính sách hoa hồng",
        $master
    );


    /**
     * ------------------------------------------------------------------------------------------------------------------------
     *              Method | URI                              | Controller @ Nethod                   | Route Name
     * ------------------------------------------------------------------------------------------------------------------------
     */

});
