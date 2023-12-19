<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class MerchantShareData
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

        if (self::$isShare) return $next($request);
        $user = $request->user();
        // $merchant_id = $user->type == User::MERCHANT ? $user->id : $user->merchant_id;
        $sidebar_menu = get_merchant_menu();
        add_js_data('crazyweb', 'get_notice_url', route('merchant.notices.get-json'));

        view()->share([
            'sidebar_menu' => $sidebar_menu,
            '_component' => 'merchant._components.', // blade path to folder contains all of components
            '_template' => 'merchant._templates.',
            '_pagination' => 'merchant._pagination.',
            '_layout' => 'merchant._layouts.',
            '_base' => 'merchant.',
        ]);
        self::$isShare = true;

        return $next($request);
    }
}
