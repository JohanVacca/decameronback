<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Auth\Factory as Auth;
use Closure;

class Authenticate extends Middleware
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if (empty($guards)) {
            $guards = ['sanctum'];
        }

        try {
            $this->authenticate($request, $guards);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return ResponseHelper::sendResponse(
                Response::HTTP_UNAUTHORIZED,
                __('auth.unauthenticated')
            );
        }

        return $next($request);
    }
}
