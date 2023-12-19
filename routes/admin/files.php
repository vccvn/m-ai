<?php

use App\Http\Controllers\Admin\General\FileController;
use Illuminate\Support\Facades\Route;


Route::controller(FileController::class)->group(function(){
    $master = admin_routes(null, true, true, true, null, "Quản lý file", "Cho phép thêm sửa xóa các file media để chèn vào nội dung sản phẩm, bài viết");
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *  Method  |  URI                           |  Method                               |  Route Name
     * --------------------------------------------------------------------------------------------------------------------
     */

    $di = Route::post('/dz-upload-image',              'dzUpload'                              )->name('.images.dz-upload');
    $gi = Route::get('/get-images',                    'getImageData'                          )->name('.images.get-data');
    $dm = Route::post('/dz-upload-media',              'dzUploadMedia'                         )->name('.media.dz-upload');
    $ck = Route::post('/ck-upload-media',              'ckUploadMedia'                         )->name('.media.ck-upload');
    $gm = Route::get('/get-media',                     'getMediaData'                          )->name('.media.get-data');

    $master->addActionByRouter($di, ['create', 'update', 'refs']);
    $master->addActionByRouter($gi, ['view', 'refs']);
    $master->addActionByRouter($dm, ['create', 'update', 'refs']);
    $master->addActionByRouter($gm, ['view', 'refs']);

});
