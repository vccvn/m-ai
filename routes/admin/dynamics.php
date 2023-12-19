<?php

use App\Http\Controllers\Admin\General\DynamicController;
use Illuminate\Support\Facades\Route;



Route::controller(DynamicController::class)->group(function(){
    $master = admin_routes(null, true, true, true, null, "Quản lý mục đăng bài", "Cho phép tạo và thay đởi thông tin các mục đăng bài");
    /**
     * --------------------------------------------------------------------------------------------------------------------------
     *            Method | URI                         | Controller @ Nethod                   | Route Name                     |
     * --------------------------------------------------------------------------------------------------------------------------
     */

    $select = Route::get('/select-options',            'getSelectOptions'         )->name('.select-option');
    $check  = Route::post('/check-slug',               'checkSlug'                )->name('.check-slug');
    $getSlug= Route::get('/get-slug',                  'getSlug'                  )->name('.get-slug');
    $master->addActionByRouter($check, ['create', 'update']);
    $master->addActionByRouter($getSlug, ['create', 'update']);
    $master->addActionByRouter($select, ['refs']);
    
    
});
