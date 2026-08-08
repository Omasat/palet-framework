<?php

declare(strict_types=1);

namespace Palet\Framework\Security\Middleware;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Contracts\Http\Middleware\MiddlewareInterface;
use Palet\Framework\Security\Http\SecurityHeadersManager;
use Closure;

class SecurityHeadersMiddleware implements MiddlewareInterface
{
    protected SecurityHeadersManager $manager;

    public function __construct(SecurityHeadersManager $manager)
    {
        $this->manager = $manager;
    }

    public function handle(RequestInterface $request, Closure $next): ResponseInterface
    {
        $response = $next($request);
        
        return $this->manager->apply($response);
    }
}
