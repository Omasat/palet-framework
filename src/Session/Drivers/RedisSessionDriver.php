<?php

declare(strict_types=1);

namespace Palet\Framework\Session\Drivers;

use SessionHandlerInterface;
use Psr\SimpleCache\CacheInterface;

class RedisSessionDriver implements SessionHandlerInterface
{
    protected CacheInterface $cache;
    protected int $minutes;
    protected string $prefix;

    public function __construct(CacheInterface $cache, int $minutes = 120, string $prefix = 'session:')
    {
        $this->cache = $cache;
        $this->minutes = $minutes;
        $this->prefix = $prefix;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $value = $this->cache->get($this->prefix . $id);
        
        if ($value !== null) {
            return $value;
        }

        return false;
    }

    public function write(string $id, string $data): bool
    {
        return $this->cache->set($this->prefix . $id, $data, $this->minutes * 60);
    }

    public function destroy(string $id): bool
    {
        return $this->cache->delete($this->prefix . $id);
    }

    public function gc(int $max_lifetime): int|false
    {
        // Redis handles GC natively via TTL
        return 0;
    }
}
