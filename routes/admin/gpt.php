<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Admin\GPT\CriteriaController;
use App\Http\Controllers\Admin\GPT\PromptController;
use App\Http\Controllers\Admin\GPT\TopicController;
use Illuminate\Support\Facades\Route;



$master = ModuleManager::addModule('admin', 'gpt', [
    'name' => 'Quản lý các chức năng của GPT',
    'description' => 'Cho phép xem sửa xoá các module của GPT',
    'prefix' => 'admin/gpt',
    'path' => 'admin.gpt'
]);



Route::prefix('topics')->name('.topics')->controller(TopicController::class)->group(function() use ($master){

    $CateMaster = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý Chủ đề", "Cho phép thêm / sửa / xóa các chủ đề",
        $master
    );


    /**
     * ------------------------------------------------------------------------------------------------------------------------
     *              Method | URI                              | Controller @ Nethod                   | Route Name
     * ------------------------------------------------------------------------------------------------------------------------
     */

    Route::get('{detail_id}', 'getDetail')->name('.topic-detail');
});



Route::prefix('prompts')->name('.prompts')->controller(PromptController::class)->group(function () use ($master) {
    $promptMaster = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý các prompt", "Cho phép quản lý Các prompt",
        $master
    );

    $promptMaster->addActionByRouter(Route::get('nhap-lieu.html', 'getImportForm')->name('.import-form'), ['create', 'update'], 'Import Form');
    $promptMaster->addActionByRouter(Route::post('import', 'import')->name('.import'), ['create', 'update'], 'Import');
    $promptMaster->addActionByRouter(Route::get('them-nhanh.html', 'getQuickAddForm')->name('.quick-add-form'), ['create', 'update'], 'Import Form');
    $promptMaster->addActionByRouter(Route::post('quick-add', 'quickAdd')->name('.quick-add'), ['create', 'update'], 'Import');

});
Route::prefix('criteria')->name('.criteria')->controller(CriteriaController::class)->group(function () use ($master) {
    $warehouseMaster = admin_routes(
        // khai bao route
        null, true, true,
        // khai bao module
        true, null, "Quản lý các Tiêu chí", "Cho phép quản lý Các tiêu chí của prompt",
        $master
    );
});
