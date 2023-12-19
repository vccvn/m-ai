<?php

/**
 * middleware phân quyền
 * @author Doan Le
 * @copyright 2019
 *
 * tác dụng Phân quyền cho các route
 */

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

use Gomee\Laravel\Router;
use App\Repositories\Permissions\ModuleRepository;
use App\Services\Permissions\PermissionService;
use ReflectionClass;

/**
 * @property-read static PermissionService $permissionService
 */
class Permission
{
    protected static $checkedModules = [];
    protected static $loaded = false;
    /**
     * Permission Service
     *
     * @var PermissionService
     */
    protected static $permissionService = null;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!self::$loaded) {
            $permissionService = app(PermissionService::class);
            $permissionService->setupModuleRoleMatrix();
            self::$loaded = true;
            self::$permissionService = $permissionService;
        }
        if (!($user = $request->user())) return $this->redirect($request);
        if ($user->status < 1)  return $this->redirect($request);
        if (strtoupper(config('system.2fa.enabled')) == 'ON') {
            if (!$user->google2fa_secret) return redirect()->route('setup-2fa');
        }
        // lấy thông tin route hiện tại
        $subDomain = get_subdomain() ;
        $routeInfo = Router::getRouteInfo($request->route());
        if($request->is('admin/3d/*'))
            return $next($request);
        if ($request->is('merchant/') || $request->is('merchant/*') || $subDomain == 'merchant') {
            return $next($request);
        }
        if (in_array($user->type, [User::ADMIN])) return $next($request);
        
        $userRoles = $user->getUserRoles();
        if (array_key_exists('name', $routeInfo) && $routeInfo['name']) {

            if (PermissionService::checkModulePermission($routeInfo['name'], $userRoles)) return $next($request);
        }
        return $this->redirect($request);
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
            return response()->json(['status' => false, 'message' => $message ? $message : 'Bạn không thể thực hiện hành động này!'], 403);
        }
        abort(403, $message ? $message : "Truy cập bị từ chối");
    }


    // chuyển hướng trang
    public function login($request)
    {
        if (0 === strpos($request->headers->get('Accept'), 'application/json')) {
            return response()->json(['status' => false, 'message' => 'Vui lòng đăng nhập', 'data' => ['url' => route('login')]], 400);
        }
        return redirect()->route('login');
    }
}
