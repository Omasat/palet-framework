<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Cookie;

interface CookieJarInterface
{
    /**
     * Create a new cookie instance.
     */
    public function make(string $name, string $value, int $minutes = 0, ?string $path = null, ?string $domain = null, ?bool $secure = null, bool $httpOnly = true, bool $raw = false, ?string $sameSite = null): mixed;

    /**
     * Create a cookie that lasts "forever" (five years).
     */
    public function forever(string $name, string $value, ?string $path = null, ?string $domain = null, ?bool $secure = null, bool $httpOnly = true, bool $raw = false, ?string $sameSite = null): mixed;

    /**
     * Expire the given cookie.
     */
    public function forget(string $name, ?string $path = null, ?string $domain = null): mixed;

    /**
     * Determine if a cookie has been queued.
     */
    public function hasQueued(string $key): bool;

    /**
     * Get a queued cookie instance.
     */
    public function queued(string $key, mixed $default = null): mixed;

    /**
     * Queue a cookie to send with the next response.
     */
    public function queue(mixed ...$parameters): void;

    /**
     * Remove a cookie from the queue.
     */
    public function unqueue(string $name): void;
}
