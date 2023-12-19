<?php

namespace App\Providers;

use App\Engines\ModuleManager;
use App\Services\Permissions\PermissionService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();


        if ($this->app->runningInConsole()) {
            return;
        }
        if (isset($_GET) && isset($_GET['active_matrix_route'])) {
            ModuleManager::active();
        }
        $this->routes(function () {
            $adminMiddleWare = ['web', 'checkinstalled', 'auth', 'https.redirect', 'permission', 'admin.share'];
            $merchantMiddleWare = ['web', 'checkinstalled', 'auth', 'https.redirect', 'merchant.share'];

            if (strtoupper(config('system.2fa.enabled')) == 'ON') {
                $adminMiddleWare[] = '2fa';
                $merchantMiddleWare[] = '2fa';
            }
            // if (($subDomain = get_subdomain()) && in_array($subDomain, ['api', 'admin', 'merchant'])) {

            //     Route::middleware(['web', 'checkinstalled', 'https.redirect'])
            //         ->group(base_path('routes/sub.php'));
            //     //
            //     switch ($subDomain) {
            //         case 'admin':

            //             Route::middleware($adminMiddleWare)
            //                 ->group(base_path('routes/admin.php'));
            //             break;
            //         case 'merchant':

            //             Route::middleware($merchantMiddleWare)
            //                 ->group(base_path('routes/merchant.php'));
            //             break;
            //         case 'api':
            //             Route::middleware(['api'])
            //                 ->group(base_path('routes/api.php'));
            //             break;

            //         default:
            //             # code...
            //             break;
            //     }

            // } else {

                Route::prefix('api')
                    ->middleware(['api', 'checkinstalled'])
                    ->group(base_path('routes/api.php'));

                Route::prefix('setup')
                    ->middleware(['web', 'checknotinstall'])
                    ->group(base_path('routes/setup.php'));

                Route::prefix('admin')
                    ->middleware($adminMiddleWare)
                    ->group(base_path('routes/admin.php'));

                Route::prefix('merchant')
                    ->middleware($merchantMiddleWare)
                    ->group(base_path('routes/merchant.php'));


                Route::middleware(['web', 'checkinstalled', 'https.redirect'])
                    ->group(base_path('routes/web.php'));
            // }
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
