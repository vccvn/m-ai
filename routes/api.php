<?php

use App\Http\Controllers\Apis\AR\ModelController;
use App\Http\Controllers\Apis\Payments\MethodController;
use App\Http\Controllers\Apis\Payments\PackageController;
use App\Http\Controllers\Apis\Payments\PaymentController;
use App\Http\Controllers\Apis\Schedules\ScheduleController;
use App\Http\Controllers\Apis\Tests\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$aa   = 'auth:api';
$auth = [$aa];

$routes = [];


Route::prefix('payment')->group(function () {
    Route::prefix('packages')->middleware(['auth:api'])->controller(PackageController::class)->group(function () {
        Route::get('/', 'getPackages');
        Route::middleware(['admin'])->group(function () {
            Route::match(['post', 'put'], 'create', 'create');
            Route::match(['post', 'patch'], 'update/{uuid?}', 'update');
        });
    });
    Route::prefix('methods')->middleware(['auth:api'])->controller(MethodController::class)->group(function () {
        Route::get('/', 'getActiveMethods');
    });
    Route::middleware(['auth:api'])->controller(PaymentController::class)->group(function () {
        Route::post('/create', 'createConnectPayment')->name('api.payment.create');
        Route::any('/status', 'checkPaymentStatus')->name('api.payment.status');
    });
    Route::controller(PaymentController::class)->group(function () {
        Route::any('/cancel', 'cancelTransaction')->name('api.payment.cancel');
        Route::any('/complete', 'completeTransaction')->name('api.payment.complete');
        Route::any('/alepay-webhook', 'alepayWebhook')->name('api.payment.alepay-webhook');
    });
});



Route::prefix('schedules')->controller(ScheduleController::class)->group(function () {
    Route::post('agent-report', 'agentReport');
});

Route::prefix('tests')->controller(TestController::class)->group(function () {
    Route::any('connect', 'testConnect');
    Route::any('require-vouchers','requireVouchers');
});

Route::prefix('ar')->name('api.ar.')->group(function(){
    Route::prefix('models')->name('models.')->controller(ModelController::class)->group(function(){
        Route::post('update-tracking', 'updateTrackingData')->name('update-tracking');
    });
});

foreach ($routes as $prefix => $middleware) {
    $mw = null; // middleware
    $pf = null; // prefix
    if (!is_numeric($prefix)) {
        $pf = $prefix;
        if ($middleware) {
            $mw = $middleware;
        }
    } else {
        $pf = $middleware;
    }
    if ($pf) {
        $router = Route::prefix($pf)->as('api.' . $prefix . '.');
        if ($mw) {
            $router->middleware($mw);
        }
        $router->group(base_path('routes/api/' . $pf . '.php'));
    }
}
