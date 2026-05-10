<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect('/admin/login');
        }

        if (auth()->user()->role !== 'admin') {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthorized.'], 403)
                : redirect('/admin/login');
        }

        return $next($request);
    }
}
