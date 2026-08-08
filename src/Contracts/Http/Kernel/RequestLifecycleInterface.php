<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http\Kernel;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;

interface RequestLifecycleInterface
{
    /**
     * Called before the request is passed to the global middleware pipeline.
     */
    public function onStart(RequestInterface $request): void;

    /**
     * Called after the response is generated but before it is sent to the client.
     */
    public function onSend(RequestInterface $request, ResponseInterface $response): void;
}
