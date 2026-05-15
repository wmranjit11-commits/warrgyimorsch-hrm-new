<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
   public function handle($request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            abort(403);
        }

        // SUPER ADMIN BYPASS
        if (auth()->user()->role === 'super_admin') {
            return $next($request);
        }

        if (!auth()->user()->hasPermission($permission)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
