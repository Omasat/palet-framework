<?php

declare(strict_types=1);

namespace Palet\Framework\Cache;

use Psr\SimpleCache\CacheInterface;
use Palet\Framework\Contracts\Cache\CacheStoreInterface;
use Closure;
use DateInterval;

class CacheRepository implements CacheStoreInterface
{
    protected CacheInterface $store;

    public function __construct(CacheInterface $store)
    {
        $this->store = $store;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->store->get($key, $default);
    }

    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        return $this->store->set($key, $value, $this->getSeconds($ttl));
    }

    public function delete(string $key): bool
    {
        return $this->store->delete($key);
    }

    public function clear(): bool
    {
        return $this->store->clear();
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        return $this->store->getMultiple($keys, $default);
    }

    public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
    {
        return $this->store->setMultiple($values, $this->getSeconds($ttl));
    }

    public function deleteMultiple(iterable $keys): bool
    {
        return $this->store->deleteMultiple($keys);
    }

    public function has(string $key): bool
    {
        return $this->store->has($key);
    }

    public function remember(string $key, int $ttl, Closure $callback): mixed
    {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }
    
    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->delete($key);
        return $value;
    }

    protected function getSeconds(null|int|DateInterval $ttl): ?int
    {
        if ($ttl instanceof DateInterval) {
            return (new \DateTime('@0'))->add($ttl)->getTimestamp();
        }
        
        return $ttl;
    }
}
