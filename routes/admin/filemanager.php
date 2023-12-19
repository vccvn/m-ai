<?php

use App\Http\Controllers\Admin\CPanel\FileEditorController;
use App\Http\Controllers\Admin\CPanel\FilemanagerController;
use Illuminate\Support\Facades\Route;



Route::get('/',                  [FilemanagerController::class,'getIndex']                      )->name('');
Route::name('.')->group(function(){
    
    /**
     * --------------------------------------------------------------------------------------------------------------------
     *  Method  |  URI                           |  Nethod                               |  Route Name                    
     * --------------------------------------------------------------------------------------------------------------------
     */

    Route::get('create-file',                    [FileEditorController::class,'createFile'] )->name('create-file');

    Route::controller(FilemanagerController::class)->group(function(){
    
        Route::post('create-folder',               'createFolder'             )->name('folders.make');
        
        Route::get('get-folder-size',              'getFolderSize'            )->name('folders.size');
        
        Route::get('upload',                       'showUploadForm'           )->name('upload.form');
        Route::post('do-upload',                   'doUpload'                 )->name('upload.save');
        Route::get('download',                     'download'                 )->name('download');
        Route::post('unzip',                       'unzip'                    )->name('items.unzip');
        
        Route::post('create',                      'createFile'               )->name('items.create');
        Route::post('rename',                      'rename'                   )->name('items.rename');
        Route::post('move-items',                  'moveItems'                )->name('items.move');
        Route::post('delete-items',                'deleteItems'              )->name('items.delete');
        
        Route::get('editor',                       'editor'                   )->name('editor');
        
        Route::post('save-file-content',           'saveFileContent'          )->name('files.save');
        Route::post('install-package',             'installPackage'           )->name('packages.install');
        Route::post('command',                     'command'                  )->name('command');
    });
    
});


/**
 * --------------------------------------------------------------------------------------------------------------------
 * |                    URI                    |       Controller @ method       |             Route Name             |
 * --------------------------------------------------------------------------------------------------------------------
 */
