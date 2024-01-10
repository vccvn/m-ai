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

    Route::get('thanh-toan-chuyen-khoan',                      'transfer'                    )->name('transfer');
    Route::get('check-order-payment',                          'checkOrderPayment'           )->name('check-order');
    Route::post('check-order-payment',                         'checkOrderPayment'           );
    Route::post('verify-transfer',                             'verifyTransfer'              )->name('verify-transfer');


});

Route::controller(PaymentServiceController::class)->middleware('web.auth')->name('payments.')->group(function () {
    Route::any('pay-options',                                  'payOptions'                  )->name('options');
    Route::any('pay-detail',                                   'packageDetail'                  )->name('detail');
    Route::post('payment/use-from-my-account',                 'useMonthFromMyAccount'       )->name('use-from-account');
    Route::post('payment/buy-package',                         'buyPackage'                  )->name('buy-package');
    Route::post('pay',                                         'pay'                         )->name('pay');

});
Route::controller(PaymentServiceController::class)->name('payments.')->group(function () {
    Route::any('package-detail/{id?}',                         'packageDetail'               )->name('package-detail');
    Route::any('payment/cancel',                               'cancelTransaction'           )->name('cancel');
    Route::any('payment/complete',                             'completeTransaction'         )->name('complete');
    Route::any('payment/alepay-webhook',                       'alepayWebhook'               )->name('alepay-webhook');
});
// useMonthFromMyAccount
