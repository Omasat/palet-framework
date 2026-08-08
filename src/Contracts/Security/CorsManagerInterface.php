<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Security;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;

interface CorsManagerInterface
{
    /**
     * Add CORS headers to the given response based on the request.
     */
    public function handle(RequestInterface $request, ResponseInterface $response): ResponseInterface;

    /**
     * Determine if the request is a CORS preflight request.
     */
    public function isPreflightRequest(RequestInterface $request): bool;

    /**
     * Handle a CORS preflight request.
     */
    public function configurePreflightResponse(RequestInterface $request, ResponseInterface $response): ResponseInterface;
}
