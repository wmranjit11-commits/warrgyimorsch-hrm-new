<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin allow
        if (($user->role === 'super_admin') || ($user->role === 'manager') || ($user->role === 'hr_executive')) {
            return $next($request);
        }

        // Example: user permissions array/json
        if (!$user->permissions || !in_array($permission, $user->permissions)) {
            abort(403, 'You are not authorised to access this page.');
        }

        return $next($request);
    }
}
