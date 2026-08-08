<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http\Kernel;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;

interface TerminableKernelInterface
{
    /**
     * Perform any final actions for the request lifecycle (logging, cleanup, etc.)
     * after the response has been sent to the browser.
     */
    public function terminateRequest(RequestInterface $request, ResponseInterface $response): void;
}
