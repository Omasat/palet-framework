<?php

declare(strict_types=1);

namespace Palet\Framework\Security\Middleware;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Contracts\Http\Middleware\MiddlewareInterface;
use Palet\Framework\Http\Response\ResponseFactory;
use Palet\Framework\Security\Cors\CorsManager;
use Closure;

class HandleCorsMiddleware implements MiddlewareInterface
{
    protected CorsManager $cors;
    protected ResponseFactory $responseFactory;

    public function __construct(CorsManager $cors, ResponseFactory $responseFactory)
    {
        $this->cors = $cors;
        $this->responseFactory = $responseFactory;
    }

    public function handle(RequestInterface $request, Closure $next): ResponseInterface
    {
        if ($this->cors->isPreflightRequest($request)) {
            $response = clone $this->responseFactory->noContent(204);
            return $this->cors->configurePreflightResponse($request, $response);
        }

        $response = $next($request);

        return $this->cors->handle($request, $response);
    }
}
