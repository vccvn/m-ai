<?php

use App\Http\Controllers\Web\Payments\PaymentController;
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



    Route::get('/vnpay',                                        'vnPay'                 )->name('vnpay');
    Route::get('/vnpay/form',                                   'vnPay'                 )->name('vnpay.form');
    Route::get('/vnpay/create',                                 'vnPayCreate'           )->name('vnpay.submit');
    Route::post('/vnpay/create',                                'vnPayCreate'           );

    Route::get('/vnpay/check',                                  'vnPayCheck'            )->name('vnpay.check');
    Route::post('/vnpay-check',                                 'vnPayCheck'            );

    Route::get('/vnpay/status',                                 'vnPayStatus'           )->name('vnpay.status');
    Route::post('/vnpay/status',                                'vnPayStatus'           );

    Route::get('/momo',                                        'momo'                 )->name('momo');
    Route::get('/momo/form',                                   'momo'                 )->name('momo.form');
    Route::get('/momo/create',                                 'momoCreate'           )->name('momo.submit');
    Route::post('/momo/create',                                'momoCreate'           );

    Route::get('/momo/check',                                  'momoCheck'            )->name('momo.check');
    Route::post('/momo-check',                                 'momoCheck'            );

    Route::get('/momo/status',                                 'momoStatus'           )->name('momo.status');
    Route::post('/momo/status',                                'momoStatus'           );
});
