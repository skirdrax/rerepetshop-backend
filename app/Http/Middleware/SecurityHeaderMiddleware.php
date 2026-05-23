<?php

namespace App\Http\Middleware;

use Closure;

class SecurityHeadersMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // D-02: Anti-clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // D-04: HSTS
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        
        // D-01: CSP
        $response->headers->set('Content-Security-Policy', "default-src 'self'");
        
        return $response;
    }
}


