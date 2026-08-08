<?php

declare(strict_types=1);

namespace Palet\Framework\Security\Middleware;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Contracts\Http\Middleware\MiddlewareInterface;
use Palet\Framework\Contracts\Security\CsrfManagerInterface;
use Palet\Framework\Security\Csrf\TokenMismatchException;
use Closure;

class VerifyCsrfTokenMiddleware implements MiddlewareInterface
{
    protected CsrfManagerInterface $csrf;

    public function __construct(CsrfManagerInterface $csrf)
    {
        $this->csrf = $csrf;
    }

    public function handle(RequestInterface $request, Closure $next): ResponseInterface
    {
        if ($this->isReading($request)) {
            return $next($request);
        }

        if ($this->tokensMatch($request)) {
            return $next($request);
        }

        throw new TokenMismatchException('CSRF token mismatch.');
    }

    protected function isReading(RequestInterface $request): bool
    {
        return in_array($request->getMethod(), ['HEAD', 'GET', 'OPTIONS']);
    }

    protected function tokensMatch(RequestInterface $request): bool
    {
        $token = $this->getTokenFromRequest($request);
        return is_string($token) && $this->csrf->validate($token);
    }

    protected function getTokenFromRequest(RequestInterface $request): ?string
    {
        $parsedBody = method_exists($request, 'getParsedBody') ? $request->getParsedBody() : null;
        if (is_array($parsedBody) && isset($parsedBody['_token'])) {
            return $parsedBody['_token'];
        }

        $headerToken = $request->getHeaderLine('X-CSRF-TOKEN');
        if (!empty($headerToken)) {
            return $headerToken;
        }
        
        $xsrfToken = $request->getHeaderLine('X-XSRF-TOKEN');
        if (!empty($xsrfToken)) {
            return $xsrfToken;
        }

        return null;
    }
}
