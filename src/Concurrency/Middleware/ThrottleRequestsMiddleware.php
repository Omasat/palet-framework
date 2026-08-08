<?php

declare(strict_types=1);

namespace Palet\Framework\Concurrency\Middleware;

use Palet\Framework\Contracts\Concurrency\RateLimiterInterface;
use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Contracts\Http\Middleware\MiddlewareInterface;
use Palet\Framework\Http\Response\ResponseFactory;
use Closure;
use RuntimeException;

class ThrottleRequestsMiddleware implements MiddlewareInterface
{
    protected RateLimiterInterface $limiter;
    protected ResponseFactory $responseFactory;
    
    // Default config
    protected int $maxAttempts = 60;
    protected int $decayMinutes = 1;

    public function __construct(RateLimiterInterface $limiter, ResponseFactory $responseFactory)
    {
        $this->limiter = $limiter;
        $this->responseFactory = $responseFactory;
    }

    public function handle(RequestInterface $request, Closure $next): ResponseInterface
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $this->maxAttempts)) {
            return $this->buildResponse($key, $this->maxAttempts);
        }

        $this->limiter->hit($key, $this->decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $this->maxAttempts,
            $this->calculateRemainingAttempts($key, $this->maxAttempts)
        );
    }

    protected function resolveRequestSignature(RequestInterface $request): string
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? '127.0.0.1';
        return sha1($ip);
    }

    protected function buildResponse(string $key, int $maxAttempts): ResponseInterface
    {
        $response = $this->responseFactory->make('Too Many Requests', 429);
        
        // Add headers for Too Many Requests
        $retryAfter = $this->limiter->availableIn($key);
        
        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts),
            $retryAfter
        );
    }

    protected function calculateRemainingAttempts(string $key, int $maxAttempts): int
    {
        return $this->limiter->remaining($key, $maxAttempts);
    }

    protected function addHeaders(ResponseInterface $response, int $maxAttempts, int $remainingAttempts, ?int $retryAfter = null): ResponseInterface
    {
        $response = $response->withHeader('X-RateLimit-Limit', (string) $maxAttempts)
                             ->withHeader('X-RateLimit-Remaining', (string) $remainingAttempts);

        if ($retryAfter !== null) {
            $response = $response->withHeader('Retry-After', (string) $retryAfter)
                                 ->withHeader('X-RateLimit-Reset', (string) (time() + $retryAfter));
        }

        return $response;
    }
}
