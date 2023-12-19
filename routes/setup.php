<?php

use App\Http\Controllers\Setup\InstallController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Setup Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
 |---------------------------------------------------------------------------------------------
 |                                         Route Table
 |---------------------------------------------------------------------------------------------
 | METHOD | URI                           | ACTIOM                        | NAME               
 |---------------------------------------------------------------------------------------------
 */
Route::controller(InstallController::class)->group(function(){
    /*
    |---------------------------------------------------------------------------------------------
    |                                         Route Table
    |---------------------------------------------------------------------------------------------
    |  METHOD  |   URI                       |   ACTIOM                      |   NAME               
    |---------------------------------------------------------------------------------------------
    */
    Route::get('install',                    'getInstallForm'                )->name('setup.install');
    Route::post('install',                   'install'                       );
});