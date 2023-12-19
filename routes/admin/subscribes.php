<?php

use App\Http\Controllers\Admin\General\SubscribeController;
use Illuminate\Support\Facades\Route;

admin_routes(SubscribeController::class, true, true, true, null, "Quản lý Người theo dõi", "Cho phép quản lý thông tin những người đã đăng ký theo dõi");
/**
 * --------------------------------------------------------------------------------------------------------------------
 *    Method | URI                           | Controller @ Nethod                   | Route Name                     |
 * --------------------------------------------------------------------------------------------------------------------
 */
