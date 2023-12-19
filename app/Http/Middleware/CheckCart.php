<?php

namespace App\Http\Middleware;

use App\Repositories\Orders\CartRepository;
use Closure;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;

class CheckCart
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!($user = auth()->user())){
            $cookieCartId = Cookie::get('cart_id');
        }else{
            $cookieCartId = null;
            if($cart = app(CartRepository::class)->first(['user_id' => $user->id])){
                $cookieCartId = $cart->id;
            }
        }
        
        if ($cookieCartId) {
            if (!is_numeric($cookieCartId)) {
                $cookieCartId = Crypt::decryptString($cookieCartId);
            }

            if ($cookieCartId) {
                CartRepository::setCartID($cookieCartId);
            }
        }
        return $next($request);
    }

}
