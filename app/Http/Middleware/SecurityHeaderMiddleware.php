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

        // D-05: MIME Sniffing Prevention
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // D-01: CSP
        $response->headers->set('Content-Security-Policy', "default-src 'self'");
        
        return $response;
    }
}


