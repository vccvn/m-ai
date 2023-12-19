<?php

use App\Http\Controllers\Admin\General\PageController;
use Illuminate\Support\Facades\Route;



Route::controller(PageController::class)->group(function(){
    $master = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý trang", "Cho phép xem danh sách, hoặc thêm / sửa / xóa thông tin các trang tĩnh"
    );
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *            Method | URI                           | Controller @ Method                   | Route Name
     * --------------------------------------------------------------------------------------------------------------------
     */

    $cl   = Route::post('/check-slug',                   'checkSlug'                             )->name('.check-slug');
    $gl   = Route::get('/get-slug',                      'getSlug'                               )->name('.get-slug');

    $master->addActionByRouter($cl, ['create', 'update']);
    $master->addActionByRouter($gl, ['create', 'update']);


});
