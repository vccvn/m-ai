<?php

/**
 * middleware phân quyền
 * @author Doan Le
 * @copyright 2019
 *
 * tác dụng Phân quyền cho các route
 */

namespace App\Http\Middleware;

use Closure;

use Gomee\Laravel\Router;
use App\Repositories\Permissions\ModuleRepository;
use App\Services\Permissions\PermissionService;
use ReflectionClass;

class Admin
{
    protected static $checkedModules = [];
    protected static $loaded = false;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!($user = $request->user())) return $this->redirect($request);
        if($user->status < 1)  return $this->redirect($request);
        
        if (in_array($user->type, ['admin', 'manager'])) return $next($request);

        return $this->redirect($request);
    }

    // chuyển hướng trang
    /**
     * Undocumented function
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function redirect($request)
    {
        if (0 === strpos($request->headers->get('Accept'), 'application/json') || $request->is('api/*')) {
            return response()->json(['status' => false, 'message' => 'Bạn không thể thực hiện hành động này!'], 403);
        }
        abort(403, "Truy cập bị từ chối");
    }
}
