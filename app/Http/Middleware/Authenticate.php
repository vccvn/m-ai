<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Response;

class Authenticate extends Middleware
{


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {

        $a = $this->authenticate($request, $guards);
        if(is_object($a) && is_a($a, Response::class))
            return $a;
        return $next($request);
    }
    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        if ($request->is('api/*'))
            return response([
                'status'=>false,
                'status_code' => Response::HTTP_UNAUTHORIZED,
                'status_text' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                'message' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
            ]);
        // abort(401);

        $this->unauthenticated($request, $guards);
    }


    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            if (!$request->is('api/*'))
                return route('login');
        }
    }
}
