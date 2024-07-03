<?php

namespace App\Http\Middleware;

use App\Repositories\Users\DeviceRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDevice
{
    protected static $checked = -1;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (self::$checked != -1) {
            return self::$checked == 1 ? $next($request) : redirect()->route('web.alert')->with([
                'type'    => 'warning',
                'message' => 'Tài khoản này đang được đăng nhập trên thiết bị khác. Hãy sử dụng mục liên hệ và cung cấp thông tin thiết bị và trình duyệt của bạn để ban quản trị có thể hỗ trợ',
                'link'    => route('web.contacts'),
                'text'    => 'Liên hệ'
            ]);
        }
        if (Auth::check()) {
            $user = Auth::user();
            $authKey = config('auth.key');
            $authToken = $request->cookie($authKey);
            $deviceRepository = new DeviceRepository();
            if ($authToken) {
                $device = $deviceRepository->first(['user_id' => $user->id, 'session_token' => $authToken]);
                if (!$device) {
                    Auth::logout();
                    return redirect()->route('web.alert')->with([
                        'type'    => 'warning',
                        'error' => 'Phiên đăng nhập không hợp lệ. Vui lòng đăng nhập lại',
                        'link'    => route('web.account.login'),
                        'text'    => 'Đăng nhập'
                    ]);
                } elseif ($device->approved) {
                    self::$checked = 1;
                    return $next($request);
                } else {
                    Auth::logout();
                    return redirect()->route('web.alert')->with([
                        'type'    => 'warning',
                        'error' => 'Tài khoản này đang được đăng nhập trên thiết bị khác. Hãy sử dụng mục liên hệ và cung cấp thông tin thiết bị và trình duyệt của bạn để ban quản trị có thể hỗ trợ',
                        'link'    => route('web.contacts'),
                        'text'    => 'Liên hệ'
                    ]);
                }
            } else {
                Auth::logout();
                return redirect()->route('web.alert')->with([
                    'type'    => 'warning',
                    'error' => 'Phiên đăng nhập không hợp lệ. Vui lòng đăng nhập lại',
                    'link'    => route('web.account.login'),
                    'text'    => 'Đăng nhập'
                ]);
            }

            Auth::logout();
        } else {
            self::$checked = 1;
        }

        return $next($request);
    }
}
