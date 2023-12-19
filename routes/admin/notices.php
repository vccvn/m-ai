<?php

use App\Http\Controllers\Admin\General\NoticeController;
use Illuminate\Support\Facades\Route;


/**
 * --------------------------------------------------------------------------------------------------------------------
 * |                    URI                    |       Controller @ method       |             Route Name             |
 * --------------------------------------------------------------------------------------------------------------------
 */

Route::get('get-user-notices',                 [NoticeController::class,'getUserNotices'])->name('.get-json');