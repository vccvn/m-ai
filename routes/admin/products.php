<?php

use App\Http\Controllers\Admin\Ecommerce\CategoryController;
use App\Http\Controllers\Admin\Ecommerce\ProductAttributeController;
use App\Http\Controllers\Admin\Ecommerce\ProductAttributeValueController;
use App\Http\Controllers\Admin\Ecommerce\ProductCollectionController;
use App\Http\Controllers\Admin\Ecommerce\ProductController;
use App\Http\Controllers\Admin\Ecommerce\ProductFilterController;
use App\Http\Controllers\Admin\Ecommerce\ProductLabelController;
use App\Http\Controllers\Admin\Ecommerce\ProductReviewController;
use App\Http\Controllers\Admin\Ecommerce\WarehouseController;
use Illuminate\Support\Facades\Route;



$controller = ProductController::class;

$master = admin_routes($controller, true, true, true, null, "Quản lý sản phẩm", "Quản lý danh sách sản phẩm, cho phép thêm sửa xóa sản phẩm, danh mục, thuộc tính, kho hàng...");

Route::controller($controller)->name('.')->group(function()use ($master){


    /**
     * --------------------------------------------------------------------------------------------------------------------
     *                Method | URI                           |  Nethod                   | Route Name
     * --------------------------------------------------------------------------------------------------------------------
     */

    $checkSlug = Route::post('/check-slug',                    'checkSlug'                            )->name('check-slug');
    $getSlug   = Route::get('/get-slug',                       'getSlug'                              )->name('get-slug');
    $getAT     = Route::get('/get-attribute-inputs',           'getAttributeByCategory'               )->name('attribute-inputs');

    $prodopts  = Route::get('/product-options',                'getProductSelectOptions'              )->name('select-options');
    $prodTags  =  Route::get('/product-tags',                  'getProductTagData'                    )->name('tag-data');

    $master->addActionByRouter($checkSlug, ['create', 'update']);
    $master->addActionByRouter($getSlug, ['create', 'update']);
    $master->addActionByRouter($getAT, ['create', 'update']);
    $master->addActionByRouter($prodopts, ['view', 'refs']);
    $master->addActionByRouter($prodTags, ['view', 'refs']);

});



Route::prefix('categories')->name('.categories')->controller(CategoryController::class)->group(function()use ($master){

    $CateMaster = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý Danh mục sản phẩm", "Cho phép thêm / sửa / xóa các danh mục sản phẩm",
        $master
    );
    /**
     * ------------------------------------------------------------------------------------------------------------------------
     *              Method | URI                              | Controller @ Nethod                   | Route Name
     * ------------------------------------------------------------------------------------------------------------------------
     */

    $checkSlug = Route::post('/check-slug',                    'checkSlug'                            )->name('.check-slug');
    $getSlug   = Route::get('/get-slug',                       'getSlug'                              )->name('.get-slug');
    // khai báo module
    $CateMaster->addActionByRouter($checkSlug, ['create', 'update']);
    $CateMaster->addActionByRouter($getSlug, ['create', 'update']);

});


/*
|--------------------------------------------------------------------------
| Attribute Routes
|--------------------------------------------------------------------------
|
| định nghĩa các route Thuộc tính sản phẩm
|
*/

Route::prefix('attributes')->name('.attributes')->group(function () use ($master){
    $controller = ProductAttributeController::class;
    $attrMaster = admin_routes(
        // khai bao route
        $controller, true, true,
        // khai bao module
        true, null, "Quản lý Thuộc tính sản phẩm", "Cho phép thêm / sửa / xóa thuộc tính, và thêm / sửa các giá trị của thuộc tính",
        $master
    );


    /**
     * --------------------------------------------------------------------------------------------------------------------------
     *       Method | URI                           | Controller @ Nethod                   | Route Name
     * --------------------------------------------------------------------------------------------------------------------------
     */

    $attrMaster->addActionByRouter(
        Route::get('/detail',[$controller,          'getAttributeDetail']                   )->name('.detail-values'),
        ['view', 'refs']
    );


    // các route cho gia tri thuộc tính
    Route::prefix('values')->name('.values')->controller(ProductAttributeValueController::class)->group(function () use($attrMaster) {

        /**
         * ----------------------------------------------------------------------------------------------------------------------
         *               Method | URI                         | Controller @ Nethod                   | Route Name
         * ----------------------------------------------------------------------------------------------------------------------
         */
        $avIndex   = Route::get('/',                          'index'                                 )->name("");
        $list      = Route::get('/list',                      'index'                                 )->name('.list');
        $detail    = Route::get('/detail/{id?}',              'detail'                                )->name('.detail');
        $create    = Route::post('/create',                   'create'                                )->name('.create');
        $store     = Route::post('/store',                    'store'                                 )->name('.store');
        $update    = Route::put('/update/{id?}',              'update'                                )->name('.update');
        $save      = Route::post('/save',                     'save'                                  )->name('.save');
        $delete    = Route::delete('/delete',                 'delete'                                )->name('.delete');

        $avMaster = $attrMaster->addSubByMasterRouter($avIndex, "Attribute Values", "Quản lý giá trị thuộc tính");
        $avMaster->addActionByRouter($list, ['view', 'refs']);
        $avMaster->addActionByRouter($detail, ['view', 'refs']);
        $avMaster->addActionByRouter($create, ['create', 'create']);
        $avMaster->addActionByRouter($update, ['update']);
        $avMaster->addActionByRouter($store, ['create', 'update']);
        $avMaster->addActionByRouter($save, ['create', 'update']);
        $avMaster->addActionByRouter($delete, ['view', 'refs']);

    });
});
Route::prefix('filters')->name('.filters')->controller(ProductFilterController::class)->group(function () use ($master) {
    $warehouseMaster = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý List sản phẩm", "Cho phép quản lý các danh sách sản phẩm sẽ được hiển thị ngoài trang chủ",
        $master
    );

    /**
     * --------------------------------------------------------------------------------------------------------------------
     *    Method | URI                           | Controller @ Nethod                   | Route Name                     |
     * --------------------------------------------------------------------------------------------------------------------
     */
    // Route::get('/options',                       $controller.'getCategoryOptions'        )->name($route.'options');
});

Route::prefix('collections')->name('.collections')->controller(ProductCollectionController::class)->group(function () use ($master) {
    $warehouseMaster = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý Bộ sưu tập", "Cho phép quản lý các danh sách sản phẩm sẽ được hiển thị ngoài trang chủ",
        $master
    );

    /**
     * --------------------------------------------------------------------------------------------------------------------
     *    Method | URI                           | Controller @ Nethod                   | Route Name                     |
     * --------------------------------------------------------------------------------------------------------------------
     */
    // Route::get('/options',                       $controller.'getCategoryOptions'        )->name($route.'options');
        /**
     * ------------------------------------------------------------------------------------------------------------------------
     *              Method | URI                              | Controller @ Nethod                   | Route Name
     * ------------------------------------------------------------------------------------------------------------------------
     */

    $checkSlug = Route::post('/check-slug',                    'checkSlug'                            )->name('.check-slug');
    $getSlug   = Route::get('/get-slug',                       'getSlug'                              )->name('.get-slug');

    $setTags   =  Route::get('/tags',                         'getCollectionTagData'                         )->name('.tag-data');
    // khai báo module
    $warehouseMaster->addActionByRouter($checkSlug, ['create', 'update']);
    $warehouseMaster->addActionByRouter($getSlug, ['create', 'update']);

    $warehouseMaster->addActionByRouter($setTags, ['create', 'update', 'refs']);
});


Route::prefix('labels')->name('.labels')->controller(ProductLabelController::class)->group(function () use ($master) {
    $labelMaster = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý Nhãn", "Cho phép quản lý Nhãn sản phẩm có thể dùng cho các mục đích phân loại hoặc hiển thị",
        $master
    );

});



Route::prefix('reviews')->name('.reviews')->group(function () use ($master) {
    $controller = ProductReviewController::class;

    $moduleRouter = admin_routes($controller, true, true, true, null, "Quản lý đánh giá sản phẩm", "Cho phép quản lý, chỉnh sửa, phê duyệt đánh giá sản phẩm từ người dùng", $master);

    /**
     * --------------------------------------------------------------------------------------------------------------------
     *    Method | URI                           | Controller @ Nethod                   | Route Name                     |
     * --------------------------------------------------------------------------------------------------------------------
     */
    $changeapprove = Route::post('change-approve',[$controller, 'changeApprove'])->name('.change-approve');
    $moduleRouter->addActionByRouter($changeapprove, 'extra');
});


Route::prefix('warehouse')->name('.warehouse')->controller(WarehouseController::class)->group(function () use ($master) {
    $warehouseMaster = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý Kho hàng", "Cho phép xem, thêm nhật ký nhập, xuất kho",
        $master
    );

    /**
     * --------------------------------------------------------------------------------------------------------------------
     *    Method | URI                           | Controller @ Nethod                   | Route Name                     |
     * --------------------------------------------------------------------------------------------------------------------
     */
    // Route::get('/options',                       $controller.'getCategoryOptions'        )->name($route.'options');
});

