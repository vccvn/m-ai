<?php

use App\Http\Controllers\Web\eCommerce\ProductController;
use Gomee\Helpers\Arr;
use Illuminate\Support\Facades\Route;
Route::controller(ProductController::class)->name('products')->group(function(){
    /**
     * -------------------------------------------------------------------------------------------------------------------------------
     *  Method | URI                                           | Controller @ Nethod         | Route Name
     * -------------------------------------------------------------------------------------------------------------------------------
     */


    Route::get('/cate/',                                       'viewCategory'                )->name('.categories.view-by-id');
    Route::get('products/{slug}.json',                         'getProductJsonData'          )->name('.json');
    Route::get('products/detail/{id?}',                        'getProductJsonData'          )->name('.data');

    Route::post('products/kiem-tra-gia',                       'checkPrice'                  )->name('.check-price');

    Route::post('products/danh-gia',                           'makeReview'                  )->name('.review');
    Route::post('products/gui-danh-gia-bang-ajax',             'ajaxMakeReview'              )->name('.ajax-review');

    $pds = product_setting();
    $prefix = $pds->category_url_prefix??'san-pham';

    $ajax = 'ajax/' . $prefix;
    Route::get($ajax,                                          'getDataProducts'             )->name(".ajax");
    Route::get($ajax.'/{slug}',                                'getDataCategory'             )->name('.ajax.categories.view-simple');
    Route::get($ajax.'/{parent}/{child}',                      'getDataCategory'             )->name('.ajax.categories.view-child');
    Route::get($ajax.'/{first}/{second}/{third}',              'getDataCategory'             )->name('.ajax.categories.view-3-level');
    Route::get($ajax.'/{first}/{second}/{third}/{fourth}',     'getDataCategory'             )->name('.ajax.categories.view-4-level');


    Route::get($prefix,                                        'viewProducts'                )->name("");
    Route::get($prefix.'/all',                                 'viewProducts'                )->name(".all");
    Route::get($prefix.'/best-sales',                          'viewBestSales'               )->name('.best-sales');
    Route::get($prefix.'/collections',                         'viewCollections'             )->name('.collections');
    Route::get($prefix.'/{slug}',                              'viewCategory'                )->name('.categories.view-simple');
    Route::get($prefix.'/{parent}/{child}',                    'viewCategory'                )->name('.categories.view-child');
    Route::get($prefix.'/{first}/{second}/{third}',            'viewCategory'                )->name('.categories.view-3-level');
    Route::get($prefix.'/{first}/{second}/{third}/{fourth}',   'viewCategory'                )->name('.categories.view-4-level');

    $prefix = $pds->product_url_prefix??'chi-tiet-san-pham';
    Route::get($prefix.'/{slug}',                                'viewProductDetail'         )->name('.detail');
    Route::get($prefix.'/{slug}/reviews',                        'getProductReviews'         )->name('.reviews');


});
