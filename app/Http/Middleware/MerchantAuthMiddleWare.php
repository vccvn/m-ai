<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Repositories\Users\UserRepository;
use Closure;
use Illuminate\Http\Request;

class MerchantAuthMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        return $next($request);
        if ($user->type == User::MERCHANT || $user->merchant_id || $user->owner_id) {
            //
        } else {
            return $this->redirect($request, 'bạn không có quyền truy cập chức năng này');
        }
        /**
         * @var UserRepository
         */
        $userRepository = app(UserRepository::class);
        if ($user->type == User::MERCHANT) {
            set_web_data('merchant_id', $user->id);
            return $next($request);
        } elseif ($user->merchant_id) {
            set_web_data('merchant_id', $user->merchant_id);
        } elseif ($user->owner_id) {
            set_web_data('merchant_id', $user->owner_id);
        }
        if($merchant_id = get_web_data('merchant_id')){
            if($merchant = $userRepository->count(['id' => $merchant_id, 'type' => User::MERCHANT])) {
                return $next($request);
            }
        }
        return $this->redirect($request, 'bạn không có quyền truy cập chức năng này');
    }


    // chuyển hướng trang
    /**
     * Undocumented function
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $message
     * @return void
     */
    public function redirect($request, $message = null)
    {
        if (0 === strpos($request->headers->get('Accept'), 'application/json')) {
            return response()->json(['status' => false, 'message' => $message ? $message : 'Bạn không thể thực hiện hành động này!'], 200);
        }
        abort(403, $message ? $message : "Truy cập bị từ chối");
    }
}
