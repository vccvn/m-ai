<?php

use App\Http\Controllers\Apis\Payments\AlePayController;
use Illuminate\Support\Facades\Route;



Route::controller(AlePayController::class)->group(function(){

    Route::any('alepay-callback',                               'onTtransaction'                 )->name('listen');

});
