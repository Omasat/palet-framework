<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http\Middleware;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;

interface TerminableMiddlewareInterface extends MiddlewareInterface
{
    /**
     * Perform any final actions for the request lifecycle.
     */
    public function terminate(RequestInterface $request, ResponseInterface $response): void;
}
