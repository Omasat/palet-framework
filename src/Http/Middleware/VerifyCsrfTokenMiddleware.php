<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Middleware;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Contracts\Session\SessionInterface;
use Palet\Framework\Http\Message\Response;
use Closure;

class VerifyCsrfTokenMiddleware
{
    protected SessionInterface $session;
    
    /**
     * @var array<int, string>
     */
    protected array $except = [];

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function handle(RequestInterface $request, Closure $next): ResponseInterface
    {
        if (
            $this->isReading($request) ||
            $this->inExceptArray($request) ||
            $this->tokensMatch($request)
        ) {
            return $next($request);
        }

        return new Response(419, [], 'CSRF token mismatch.');
    }

    protected function isReading(RequestInterface $request): bool
    {
        return in_array($request->getMethod(), ['HEAD', 'GET', 'OPTIONS']);
    }

    protected function inExceptArray(RequestInterface $request): bool
    {
        $path = ltrim($request->getUri()->getPath(), '/');
        
        foreach ($this->except as $except) {
            $except = ltrim($except, '/');
            if ($except === $path) {
                return true;
            }
        }
        
        return false;
    }

    protected function tokensMatch(RequestInterface $request): bool
    {
        $token = $this->getTokenFromRequest($request);
        
        // Session manager might not have the method 'token', but Store does.
        // It's common to store token as '_token' in attributes.
        $sessionToken = method_exists($this->session, 'token') 
            ? $this->session->token() 
            : $this->session->get('_token');

        return is_string($sessionToken) && is_string($token) && hash_equals($sessionToken, $token);
    }

    protected function getTokenFromRequest(RequestInterface $request): ?string
    {
        $body = $request->getParsedBody();
        $token = is_array($body) ? ($body['_token'] ?? null) : null;
        
        if (!$token) {
            $header = $request->getServerParams()['HTTP_X_CSRF_TOKEN'] ?? null;
            if ($header) {
                $token = $header;
            }
        }

        return $token;
    }
}
