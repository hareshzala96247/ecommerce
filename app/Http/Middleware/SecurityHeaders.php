<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Content-Security-Policy', $this->csp());

        return $response;
    }

    private function csp(): string
    {
        if (app()->environment('local', 'development')) {
            // Allow Vite HMR and Google Fonts in development
            $viteHost = 'http://127.0.0.1:5173 http://127.0.0.1:5174 ws://127.0.0.1:5173 ws://127.0.0.1:5174';
            return implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' {$viteHost}",
                "style-src 'self' 'unsafe-inline' {$viteHost} https://fonts.googleapis.com",
                "font-src 'self' data: https://fonts.gstatic.com",
                "img-src 'self' data: https: blob:",
                "connect-src 'self' {$viteHost}",
            ]);
        }

        // Strict CSP for production
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: https: blob:",
            "connect-src 'self'",
        ]);
    }
}
