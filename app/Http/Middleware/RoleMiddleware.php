<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
         if (! $request->user() || ! $request->user()->hasRole($role)) {
            return response()->json([
                'You are not an admin, so you cannot access this data.'
            ], 403);
        }

        return $next($request);
    }
}
