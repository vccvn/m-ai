<?php

use App\Http\Controllers\Web\Payments\PaymentController;
use App\Http\Controllers\Web\Payments\PaymentServiceController;
use Illuminate\Support\Facades\Route;



$rp = 'web.payments.';
$p = 'PaymentController@';

Route::name('payments.')->controller(PaymentController::class)->group(function(){
    /**
     * -------------------------------------------------------------------------------------------------------------------------------
     *  Method | URI                                           | Controller @ Nethod         | Route Name
     * -------------------------------------------------------------------------------------------------------------------------------
     */

    Route::get('thanh-toan-chuyen-khoan',                      'transfer'              )->name('transfer');
    Route::get('check-order-payment',                           'checkOrderPayment'     )->name('check-order');
    Route::post('check-order-payment',                          'checkOrderPayment'     );
    Route::post('verify-transfer',                              'verifyTransfer'        )->name('verify-transfer');


});

Route::controller(PaymentServiceController::class)->group(function () {
    Route::any('payment/cancel', 'cancelTransaction')->name('payments.cancel');
    Route::any('payment/complete', 'completeTransaction')->name('payments.complete');
    Route::any('payment/alepay-webhook', 'alepayWebhook')->name('payments.alepay-webhook');
});
