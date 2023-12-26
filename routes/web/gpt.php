<?php

use App\Http\Controllers\Web\AI\ChatController;
use Illuminate\Support\Facades\Route;


/**
 * -------------------------------------------------------------------------------------------------------------------------------
 *  Method | URI                                           | Controller @ Nethod         | Route Name
 * -------------------------------------------------------------------------------------------------------------------------------
 */


Route::middleware('web.auth')->prefix('ai')->name('ai.')->group(function(){
    Route::controller(ChatController::class)->group(function(){

        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/',                                         'index'             )->name('index');
            Route::get('messages',                                  'index'             )->name('messages');

            Route::post('send-message',                             'sendMessage'       )->name('send-message');

            Route::any('prompt-inputs',                             'getPromptInputs'   )->name('prompt-inputs');
        });

    });

});

