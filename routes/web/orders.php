<?php

use App\Http\Controllers\Web\eCommerce\CartController;
use App\Http\Controllers\Web\eCommerce\CustomerController;
use App\Http\Controllers\Web\eCommerce\OrderController;
use Illuminate\Support\Facades\Route;


/**
 * -------------------------------------------------------------------------------------------------------------------------------
 *  Method | URI                                           | Controller @ Nethod         | Route Name
 * -------------------------------------------------------------------------------------------------------------------------------
 */


Route::middleware('cart.check')->name('orders.')->group(function(){
    Route::controller(CartController::class)->group(function(){


        $cartSlug = 'gio-hang.html';
        $addToCartSlug = 'Them-gio-hang';
        $checkoutSlug = 'dat-hang.html';

        Route::get($cartSlug,                                   'viewCart'              )->name('cart');
        Route::get($checkoutSlug,                               'checkout'              )->name('checkout');




        Route::prefix('cart')->group(function () {
            Route::post('check-price',                              'checkPrice'        )->name('check-price');
            Route::post('add',                                      'addToCart'         )->name('add-to-cart');
            Route::post('add-item',                                 'addItem'           )->name('add-cart-item');
            Route::post('add-many-item',                            'addManyItem'       )->name('add-many-item');
            Route::post('add-combo',                                'addSetCombo'       )->name('add-cart-combo');
            Route::post('check-data',                               'checkData'         )->name('check-cart-data');
            Route::post('update-cart-quantity',                     'updateCartQuantity')->name('update-cart-quantity');
            Route::post('update-item',                              'updateItem'        )->name('update-cart-item');
            Route::post('update-cart',                              'updateCart'        )->name('update-cart');
            Route::post('APPPLY_COUPON',                            'applyCoupon'       )->name('apply-coupon');
            Route::delete('empty',                                  'empty'             )->name('empty-cart');
            Route::match(['delete', 'post'], 'remove-item/{id?}',   'removeItem'        )->name('remove-cart-item');
            Route::get('get-data',                                  'checkData'         )->name('get-cart-data');




        });

        Route::post('buy-now',                                     'addItemBuyNow'     )->name('buy-now');
        Route::post('buy-now-combo',                               'buyNowSetCombo'    )->name('buy-now-combo');
        Route::get('mua-ngay.html',                                'viewBuyNowCart'    )->name('buy-now-cart');
    });

    Route::post('dat-hang',           [OrderController::class,'placeOrder']             )->name('place-order');

});



Route::controller(OrderController::class)->name('orders.')->group(function(){


    Route::get('xac-thuc-don-hang/{token}',                     'confirmOrder'          )->name('confirm');
    Route::get('quan-ly-don-hang.html',                         'manager'               )->name('manager');
    Route::get('quan-ly-don-hang/{status_key}.html',            'manager'               )->name('list');
    // Route::get('chi-tiet-don-hang/{id}.html',                   'viewDetail'            )->name('detail');
    Route::get('chi-tiet-don-hang/{code}.html',                   'viewDetail'            )->name('detail');



    Route::prefix('orders')->group(function () {
        Route::post('cancel',                                   'cancel'                )->name('cancel');
    });
});



Route::name('customers.')->controller(CustomerController::class)->group(function(){
    Route::get('dang-nhap-khach-hang.html',                     'login'          )->name('login');
    Route::post('dang-nhap-khach-hang.html',                    'createToken'    );
    Route::get('xac-minh-khach-hang/{token}',                   'verify'         )->name('verify');
    Route::get('dang-xuat-khach-hang.html',                     'logout'         )->name('logout');

});
