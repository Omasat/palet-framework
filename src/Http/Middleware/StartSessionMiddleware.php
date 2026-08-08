<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Middleware;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Contracts\Session\SessionInterface;
use Closure;

class StartSessionMiddleware
{
    protected SessionInterface $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function handle(RequestInterface $request, Closure $next): ResponseInterface
    {
        $sessionCookieName = 'palet_session';
        $cookies = $request->getCookieParams();
        
        $sessionId = $cookies[$sessionCookieName] ?? null;
        if (method_exists($this->session, 'setId')) {
            $this->session->setId($sessionId);
        }
        
        $this->session->start();
        
        /** @var ResponseInterface $response */
        $response = $next($request);
        
        $this->session->save();
        
        // Add secure cookie headers (SameSite=Lax, HttpOnly=true, Secure=true in production)
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $sameSite = 'Lax';
        $httpOnly = true;
        
        $cookieValue = method_exists($this->session, 'getId') ? $this->session->getId() : '';
        $expires = gmdate('D, d-M-Y H:i:s T', time() + (120 * 60)); // 2 hours
        
        $cookieHeader = sprintf(
            '%s=%s; expires=%s; path=/; %s%s samesite=%s',
            $sessionCookieName,
            urlencode($cookieValue),
            $expires,
            $secure ? 'secure; ' : '',
            $httpOnly ? 'httponly;' : '',
            $sameSite
        );
        
        return $response->withAddedHeader('Set-Cookie', $cookieHeader);
    }
}
