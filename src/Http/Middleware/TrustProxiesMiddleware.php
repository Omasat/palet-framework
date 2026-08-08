<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Middleware;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;

class TrustProxiesMiddleware
{
    protected array $proxies = [];

    /**
     * Handle an incoming request.
     */
    public function handle(RequestInterface $request, \Closure $next): ResponseInterface
    {
        // In a real implementation this would check if the remote IP is in trusted proxies,
        // and if so, trust the X-Forwarded-* headers (e.g. X-Forwarded-For, X-Forwarded-Proto)
        // Since PSR-7 requests are usually immutable and don't have built-in trust-proxy logic like Symfony,
        // you would modify the request's URI scheme, host, or client IP based on these headers.
        
        // This is a placeholder for the actual trusted proxy logic.
        $trusted = false;
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? null;
        
        if ($remoteAddr && in_array($remoteAddr, $this->proxies, true)) {
            $trusted = true;
        } elseif ($this->proxies === ['*']) {
            $trusted = true;
        }
        
        if ($trusted) {
            // Apply X-Forwarded-Proto
            if ($request->hasHeader('X-Forwarded-Proto')) {
                $proto = $request->getHeaderLine('X-Forwarded-Proto');
                $uri = $request->getUri()->withScheme($proto);
                $request = $request->withUri($uri);
            }
            
            // Apply X-Forwarded-Host
            if ($request->hasHeader('X-Forwarded-Host')) {
                $host = $request->getHeaderLine('X-Forwarded-Host');
                $uri = $request->getUri()->withHost($host);
                $request = $request->withUri($uri);
            }
        }

        return $next($request);
    }
}
