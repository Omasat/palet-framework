<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Middleware;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(RequestInterface $request, \Closure $next): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = $next($request);

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Referrer-Policy' => 'no-referrer-when-downgrade',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'",
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Embedder-Policy' => 'require-corp',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ];

        foreach ($headers as $key => $value) {
            if (!$response->hasHeader($key)) {
                $response = $response->withHeader($key, $value);
            }
        }

        return $response;
    }
}
