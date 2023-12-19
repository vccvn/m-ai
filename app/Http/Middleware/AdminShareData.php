<?php

namespace App\Http\Middleware;

use Closure;

class AdminShareData
{
    public static $isShare = false;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // chay qua cai nay de share data thoi

        if(self::$isShare) return $next($request);
        $menu_file = get_web_type();
        $sidebar_menu = get_admin_menu($menu_file);
        add_js_data('crazyweb','get_notice_url', route('admin.notices.get-json'));
        // add_js_data('location_data','urls', [
        //     'region_options' => route("admin.location.regions.options"),
        //     'district_options' => route("admin.location.districts.options"),
        //     'ward_options' => route("admin.location.wards.options")
        // ]);
        view()->share([
            'sidebar_menu' => $sidebar_menu,
            '_component' => 'admin._components.', // blade path to folder contains all of components
            '_template' => 'admin._templates.',
            '_pagination' => 'admin._pagination.',
            '_layout' => 'admin._layouts.',
            '_base' => 'admin.',
        ]);
        self::$isShare = true;

        return $next($request);
    }
}
