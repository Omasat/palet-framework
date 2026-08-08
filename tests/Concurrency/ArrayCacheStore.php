<?php

declare(strict_types=1);

namespace Tests\Concurrency;

use Palet\Framework\Contracts\Cache\CacheStoreInterface;

class ArrayCacheStore implements CacheStoreInterface
{
    protected array $storage = [];
    protected array $expires = [];

    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->has($key)) {
            return $default;
        }
        return $this->storage[$key];
    }

    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null): bool
    {
        $this->storage[$key] = $value;
        if ($ttl !== null) {
            $this->expires[$key] = time() + (is_int($ttl) ? $ttl : $ttl->s);
        }
        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->storage[$key]);
        unset($this->expires[$key]);
        return true;
    }

    public function clear(): bool
    {
        $this->storage = [];
        $this->expires = [];
        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        return [];
    }

    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null): bool
    {
        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        return true;
    }

    public function has(string $key): bool
    {
        if (isset($this->expires[$key]) && $this->expires[$key] < time()) {
            $this->delete($key);
            return false;
        }
        return isset($this->storage[$key]);
    }

    public function remember(string $key, int $ttl, \Closure $callback): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }
        $value = $callback();
        $this->set($key, $value, $ttl);
        return $value;
    }
}
