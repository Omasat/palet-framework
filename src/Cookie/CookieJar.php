<?php

declare(strict_types=1);

namespace Palet\Framework\Cookie;

use Palet\Framework\Contracts\Cookie\CookieJarInterface;

class CookieJar implements CookieJarInterface
{
    protected string $path = '/';
    protected string $domain = '';
    protected bool $secure = false;
    protected string $sameSite = 'Lax';
    protected array $queued = [];

    public function make(string $name, string $value, int $minutes = 0, ?string $path = null, ?string $domain = null, ?bool $secure = null, bool $httpOnly = true, bool $raw = false, ?string $sameSite = null): Cookie
    {
        $path = $path ?: $this->path;
        $domain = $domain ?: $this->domain;
        $secure = $secure ?? $this->secure;
        $sameSite = $sameSite ?: $this->sameSite;

        return new Cookie($name, $value, $minutes, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }

    public function forever(string $name, string $value, ?string $path = null, ?string $domain = null, ?bool $secure = null, bool $httpOnly = true, bool $raw = false, ?string $sameSite = null): Cookie
    {
        return $this->make($name, $value, 2628000, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }

    public function forget(string $name, ?string $path = null, ?string $domain = null): Cookie
    {
        return $this->make($name, '', -2628000, $path, $domain);
    }

    public function hasQueued(string $key): bool
    {
        return isset($this->queued[$key]);
    }

    public function queued(string $key, mixed $default = null): ?Cookie
    {
        return $this->queued[$key] ?? $default;
    }

    public function queue(mixed ...$parameters): void
    {
        if (isset($parameters[0]) && $parameters[0] instanceof Cookie) {
            $cookie = $parameters[0];
        } else {
            $cookie = $this->make(...$parameters);
        }

        $this->queued[$cookie->getName()] = $cookie;
    }

    public function unqueue(string $name): void
    {
        unset($this->queued[$name]);
    }

    public function getQueuedCookies(): array
    {
        return $this->queued;
    }
}
