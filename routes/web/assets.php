<?php

use App\Http\Controllers\Web\Common\AssetController;
use Illuminate\Support\Facades\Route;

$c = 'AssetController@';
$route = 'web.assets.';

$controller = 'AssetController@';

Route::name('assets.')->controller(AssetController::class)->group(function () {
    Route::get(
        '/static/users/{client_id}/{ref}/{width}x{height}/{filename}',
        'getImage'
    )
        ->name('user-image')
        ->where([
            'width' => '[0-9]+',
            'height' => '[0-9]+'
        ]);

    Route::get(
        '/images/users/{client_id}/{ref}/{width}x{height}/{filename}',
        'getImage'
    )
        ->name('user-images')
        ->where([
            'width' => '[0-9]+',
            'height' => '[0-9]+'
        ]);

    Route::get('/static/{ref}/{width}x{height}/{filename}', 'getImage')
        ->name('image')
        ->where([
            'width' => '[0-9]+',
            'height' => '[0-9]+'
        ]);
});
