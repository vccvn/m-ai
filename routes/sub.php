<?php

use App\Http\Controllers\Apis\Requires\RequireController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\Common\LocationController;
use App\Http\Controllers\Web\Files\ReportController;
use App\Http\Controllers\Web\Home\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any('home', function(){
    return redirect('https://' . get_cfg_domain());
})->name('home');
// test
Route::any('/check-health', [HomeController::class, 'checkHealth']);
Route::controller(AuthController::class)->group(base_path('routes/auth.php'));


Route::get('/payment/authenticate', [HomeController::class, 'authenticate'])->name('web.payment.authenticate');
Route::get('/payment/cancel', [HomeController::class, 'cancel'])->name('web.payment.cancel');

Route::get('sfiles/reports/{filename}', [ReportController::class, 'download'])->name('web.files.reports.download');
