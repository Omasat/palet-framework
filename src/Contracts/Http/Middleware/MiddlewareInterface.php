<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http\Middleware;

use Closure;
use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;

interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     */
    public function handle(RequestInterface $request, Closure $next): ResponseInterface;
}
