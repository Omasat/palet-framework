<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http\Kernel;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;

interface HttpKernelInterface
{
    /**
     * Handle an incoming HTTP request and return a Response.
     */
    public function handle(RequestInterface $request): ResponseInterface;
}
