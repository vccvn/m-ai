<?php

use App\Engines\ModuleManager;
use App\Http\Controllers\Admin\General\DashboardController;
use App\Http\Controllers\Admin\General\DynamicController;
use App\Http\Controllers\Admin\General\ErrorController;
use Gomee\Core\System;
use Gomee\Laravel\Router;
use Illuminate\Support\Facades\Route;
ModuleManager::addScope('admin', 'Trang Quản trị');
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::controller(DashboardController::class)->group(function () {
    $admin       = Route::get('/',                               'getIndex')->middleware('auth')->name('dashboard');
    $dashboard   = Route::get('/dashboard',                      'getIndex')->middleware('auth')->name('admin.dashboard');
    $showConfig  = Route::get('/config',                         'showConfig')->middleware('auth')->name('admin.config');
    $sendmail    = Route::get('sendmail',                        'sendMail')->middleware('auth')->name('admin.sendmail');

    $systemUpdateData = Route::get('update-system-data', 'updateSystemData')->name('admin.system.update-data');
    $refreshToken = Route::any('refresh-csrf-token', 'refreshCSRFToken')->name('admin.csrf-token.refresh');

    $adminMaster = ModuleManager::addModuleByRouter($admin, 'Trang Quản trị', 'Màn hình đầu tiên khi đang nhập quản trị', 'dashboard', 'admin');
    $adminMaster->addActionByRouter($dashboard, ['view'], 'Màn hình dashboard');
    $adminMaster->addActionByRouter($systemUpdateData, ['update'], 'Update dữ liệu hệ thống');

});




$routes = [
    // prefix => middleware

    // thêm route ở đây
];
$modules = get_web_module_list(get_web_type());
if (count($modules)) {
    foreach ($modules as $module) {
        if ($module) {
            $routes[] = $module;
        }
    }
}

$routes[] = 'notices';

Route::name('admin.')->group(function () use ($routes) {


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
        // if($pf == 'posts') continue;
        if ($pf && file_exists($path = base_path('routes/admin/' . $pf . '.php'))) {
            $router = Route::prefix($pf);
            if ($mw) {
                $router->middleware($mw);
            }
            $router->as($pf);
            $router->group($path);
        }
    }

    if($packageRoutes = System::getAllRoutes()){
        foreach ($packageRoutes as $slug => $scopeRoutes) {
            // chỉ lấy các route admin
            if(array_key_exists('admin', $scopeRoutes) && $scopeRoutes['admin']){
                $routes = $scopeRoutes['admin'];
                foreach ($routes as $key => $routeParam) {
                    if($routeParam['prefix']){
                        $router = Route::prefix($routeParam['prefix']);
                        if($routeParam['name']){
                            $router->name($routeParam['name']);
                        }
                        if($routeParam['middleware']){
                            $router->middleware($middleware['middleware']);
                        }
                    }elseif($routeParam['name']){
                        $router = Route::name($routeParam['name']);
                        if($routeParam['middleware']){
                            $router->middleware($middleware['middleware']);
                        }
                    }elseif($routeParam['middleware']){
                        $router = Route::middleware($routeParam['name']);
                    }
                    else{
                        $router = Route::middleware('next');
                    }
                    $router->group($routeParam['group']);
                }
            }
        }
    }




    // Route::middleware(['dynamic.post'])->name('posts')->group(base_path('routes/admin/posts.php'));
});


Route::any('{any}', [ErrorController::class, 'handleError'])->where('any', '.*')->name('error');
