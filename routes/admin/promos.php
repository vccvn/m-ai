<?php

use App\Http\Controllers\Admin\Ecommerce\PromoController;
use Illuminate\Support\Facades\Route;

$promoMaster = admin_routes(
    PromoController::class, true, true, 
    true, null, "Quản lý khuyến mãi", "Cho phép quảng lý các chương trình khuyến mãi theo từng diều kiện và thời gian cũ thể"
);
/**
 * --------------------------------------------------------------------------------------------------------------------
 *    Method | URI                           | Controller @ Nethod                   | Route Name                     |
 * --------------------------------------------------------------------------------------------------------------------
 */
// Route::get('/parent-options',                 $controller.'getParentSelectOptions'   )->name($route.'parent-option');
// Route::get('/user-tag-data',                  $controller.'getUserTagData'           )->name($route.'tag-data');


$prodopts  = Route::get('/product-options', [PromoController::class,'getPromoSelectOptions'] )->name('.select-options');
$promoMaster->addActionByRouter($prodopts, ['view', 'create', 'update', 'refs']);
