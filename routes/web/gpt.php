<?php

use App\Http\Controllers\Web\AI\ChatController;
use App\Http\Controllers\Web\AI\PromptController;
use App\Http\Controllers\Web\AI\TopicController;
use Illuminate\Support\Facades\Route;


/**
 * -------------------------------------------------------------------------------------------------------------------------------
 *  Method | URI                                           | Controller @ Nethod         | Route Name
 * -------------------------------------------------------------------------------------------------------------------------------
 */


Route::prefix('ai')->name('ai.')->group(function(){
    Route::any('/',                                       [TopicController::class, 'getIndex'])->name('index');
    Route::any('/topic/{id?}',                            [TopicController::class, 'getTopic'])->name('topic');
    Route::any('tools/search',                            [PromptController::class, 'getSearchResults'])->name('tools.search');
    Route::controller(ChatController::class)->prefix('chat')->name('chat.')->middleware(['web.auth', 'check.device'])->group(function(){
        Route::get('/',                                         'index'             )->name('index');
        Route::get('messages',                                  'index'             )->name('messages');
        Route::post('send-message',                             'sendMessage'       )->name('send-message');
        Route::any('prompt-inputs',                             'getPromptInputs'   )->name('prompt-inputs');
        Route::get('box',                                       'chatBox'           )->name('box');
        Route::post('send-box-message',                         'sendBoxMessage'    )->name('send-box-message');
        Route::any('get-chat-data',                             'getChatData'       )->name('data');
        Route::any('get-history',                               'getHistory'        )->name('get-history');

    });
    Route::controller(TopicController::class)->prefix('chuyen-de')->name('topics.')->middleware(['web.auth', 'check.device'])->group(function(){
        Route::get('/',                                         'getTopic'          )->name('detail-by-id');
        Route::get('/{slug}',                                   'getTopic'          )->name('detail-by-slug');
    });



});

