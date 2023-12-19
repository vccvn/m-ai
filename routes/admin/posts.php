<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Admin\General\DynamicController;
use App\Http\Controllers\Admin\General\PostCategoryController;
use App\Http\Controllers\Admin\General\PostController;
use Illuminate\Support\Facades\Route;
$master = ModuleManager::addModule('admin', 'posts', [
    'name' => 'Quản lý Mục đăng bài',
    'description' => 'Quản lý các mục đăng bài như tin tức, blog, hay các nội dung cho SEO',
    'route' => 'admin.posts'
]);

$cate = Route::get('categorie/options',[DynamicController::class, 'getCategoryOptions'])->name('.category-options');
$master->addActionByRouter($cate, ['view', 'create', 'update', 'refs']);
Route::controller(PostCategoryController::class)->name('.categories')->middleware(['dynamic.post'])->group(function () use($master) {
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *                Method | URI                                    | Controller @ Nethod              | Route Name               
     * --------------------------------------------------------------------------------------------------------------------
     */
    $categories = Route::get('/{dynamic}/categories',                  'getIndex'                        )->name('');
    $list       = Route::get('/{dynamic}/categories/list.html',        'getList'                         )->name('.list');
    $trash      = Route::get('/{dynamic}/categories/trash.html',       'getTrash'                        )->name('.trash');
    $ajaxSearxh = Route::get('/{dynamic}/categories/ajax-search',      'ajaxSearch'                      )->name('.ajax');
    $create     = Route::get('/{dynamic}/categories/create.html',      'getCreateForm'                   )->name('.create');
    $update     = Route::get('/{dynamic}/categories/update/{id}.html', 'getUpdateForm'                   )->name('.update');
    $save       = Route::post('/{dynamic}/categories/save',            'save'                            )->name('.save');
    $moveToTrash= Route::post('/{dynamic}/categories/move-to-trash',   'moveToTrash'                     )->name('.move-to-trash');
    $delete     = Route::post('/{dynamic}/categories/delete',          'delete'                          )->name('.delete');
    $restore    = Route::post('/{dynamic}/categories/restore',         'restore'                         )->name('.restore');
    $options    = Route::get('/{dynamic}/categories/options',          'getCategoryOptions'              )->name('.options');

    $cateMaster = $master->addSubByMasterRouter($categories, "Danh mục bài viết", "Quản lý thêm sửa xóa các danh mục bài viết hiển thị trên trang client");
    $cateMaster->addActionByRouter($list, ['view']);
    $cateMaster->addActionByRouter($trash, ['view', 'delete', 'restore']);
    $cateMaster->addActionByRouter($ajaxSearxh, ['view']);
    $cateMaster->addActionByRouter($create, ['create']);
    $cateMaster->addActionByRouter($update, ['update']);
    $cateMaster->addActionByRouter($save, ['create', 'update']);
    $cateMaster->addActionByRouter($moveToTrash, ['delete']);
    $cateMaster->addActionByRouter($delete, ['delete']);
    $cateMaster->addActionByRouter($restore, ['restore']);
    $cateMaster->addActionByRouter($options, ['view', 'refs']);
    
});
Route::controller(PostController::class)->name('.')->middleware(['dynamic.post'])->group(function() use($master){
    /**
     * -----------------------------------------------------------------------------------------------------------------------------------
     *                 Method | URI                                     | Nethod                           | Route Name               
     * -----------------------------------------------------------------------------------------------------------------------------------
     */
    $list        = Route::get('/{dynamic}/list.html',                   'getList'                          )->name('list');
    $trash       = Route::get('/{dynamic}/trash.html',                  'getTrash'                         )->name('trash');
    $ajaxSearxh  = Route::get('/{dynamic}/ajax-search',                 'ajaxSearch'                       )->name('ajax');
    $create      = Route::get('/{dynamic}/create.html',                 'getCreateForm'                    )->name('create');
    $update      = Route::get('/{dynamic}/update/{id}.html',            'getUpdateForm'                    )->name('update');
    $config      = Route::get('/{dynamic}/config.html',                 'getPostConfigForm'                )->name('config');
    $formLayout  = Route::get('/{dynamic}/form-layout.html',            'getFormLayoutSetting'             )->name('form-layout');
    $save        = Route::post('/{dynamic}/save',                       'save'                             )->name('save');
    $moveToTrash = Route::post('/{dynamic}/move-to-trash',              'moveToTrash'                      )->name('move-to-trash');
    $delete      = Route::post('/{dynamic}/delete',                     'delete'                           )->name('delete');
    $restore     = Route::post('/{dynamic}/restore',                    'restore'                          )->name('restore');
    $saveConfig  = Route::post('/{dynamic}/save-config',                'saveConfig'                       )->name('save-config');
    $delFormGroup= Route::post('/{dynamic}/delete-form-group',          'deleteFormGroup'                  )->name('delete-form-group');
    $checkSlug   = Route::post('/{dynamic}/check-slug',                 'checkSlug'                        )->name('check-slug');
    $getSlug     = Route::get('/{dynamic}/get-slug',                    'getSlug'                          )->name('get-slug');
    

    $master->addActionByRouter($list, ['view']);
    $master->addActionByRouter($trash, ['view', 'delete', 'restore']);
    $master->addActionByRouter($ajaxSearxh, ['view']);
    $master->addActionByRouter($create, ['create']);
    $master->addActionByRouter($update, ['update']);
    $master->addActionByRouter($save, ['create', 'update']);
    $master->addActionByRouter($getSlug, ['create', 'update']);
    $master->addActionByRouter($checkSlug, ['create', 'update']);
    $master->addActionByRouter($moveToTrash, ['delete']);
    $master->addActionByRouter($delete, ['delete']);
    $master->addActionByRouter($restore, ['restore']);

    $master->addActionByRouter($config, ['config']);
    $master->addActionByRouter($saveConfig, ['config']);

    $master->addActionByRouter($formLayout, ['config']);
    $master->addActionByRouter($delFormGroup, ['config']);

});
Route::controller(PostController::class)->middleware(['dynamic.post'])->group(function(){
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *  Method | URI                                    | Controller @ Nethod              | Route Name               
     * --------------------------------------------------------------------------------------------------------------------
     */

    $master = Route::get('/{dynamic}.html',                        'getIndex'                         )->name('');

    ModuleManager::addModuleByRouter($master, 'Quản lý Tin bài', 'Quản lý danh sách bài như tin tức, blog, hay các nội dung cho SEO');

});