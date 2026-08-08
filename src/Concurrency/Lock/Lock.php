<?php

declare(strict_types=1);

namespace Palet\Framework\Concurrency\Lock;

use Palet\Framework\Contracts\Concurrency\LockInterface;
use Palet\Framework\Contracts\Cache\CacheStoreInterface;

class Lock implements LockInterface
{
    protected CacheStoreInterface $cache;
    protected string $name;
    protected int $seconds;
    protected string $owner;

    public function __construct(CacheStoreInterface $cache, string $name, int $seconds, ?string $owner = null)
    {
        $this->cache = $cache;
        $this->name = $name;
        $this->seconds = $seconds;
        $this->owner = $owner ?: bin2hex(random_bytes(16));
    }

    public function acquire(): bool
    {
        // Simple cache-based lock acquisition. In production with Redis, set() with NX and EX is needed.
        // For PSR-16, we do a basic has/set if it's not strictly atomic on all drivers.
        if ($this->cache->has($this->name)) {
            return false;
        }

        $this->cache->set($this->name, $this->owner, $this->seconds);
        return true;
    }

    public function release(): bool
    {
        if ($this->isOwnedByCurrentProcess()) {
            $this->cache->delete($this->name);
            return true;
        }

        return false;
    }

    public function forceRelease(): void
    {
        $this->cache->delete($this->name);
    }

    public function owner(): string
    {
        return $this->owner;
    }

    public function name(): string
    {
        return $this->name;
    }

    protected function isOwnedByCurrentProcess(): bool
    {
        return $this->cache->get($this->name) === $this->owner;
    }
}
