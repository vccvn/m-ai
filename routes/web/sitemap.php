<?php

use App\Http\Controllers\Web\Common\SitemapController;
use Illuminate\Support\Facades\Route;

$controller = SitemapController::class;
Route::controller(SitemapController::class)->name('.')->group(function(){
    Route::get('/sitemap.xml','sitemap')->name('index');
    Route::get('/sitemap/home.xml','home')->name('home');
    Route::get('/sitemap/category.xml','productCategory')->name('product-category');
    Route::get('/sitemap/products.xml','products')->name('products');
    Route::get('/sitemap/{slug}.xml','posts')->name('posts');


});
