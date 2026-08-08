<?php

declare(strict_types=1);

namespace Palet\Framework\Cache\Drivers;

use Psr\SimpleCache\CacheInterface;
use Memcached;

class MemcachedDriver implements CacheInterface
{
    protected Memcached $memcached;

    public function __construct(Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->memcached->get($key);

        if ($this->memcached->getResultCode() === Memcached::RES_NOTFOUND) {
            return $default;
        }

        return $value;
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $expiration = 0;
        
        if ($ttl !== null) {
            $seconds = $ttl instanceof \DateInterval ? (new \DateTime('@0'))->add($ttl)->getTimestamp() : $ttl;
            
            if ($seconds <= 0) {
                return $this->delete($key);
            }
            
            // Memcached TTL limit is 30 days (2592000 seconds). If larger, use unix timestamp
            $expiration = $seconds > 2592000 ? time() + $seconds : $seconds;
        }

        return $this->memcached->set($key, $value, $expiration);
    }

    public function delete(string $key): bool
    {
        return $this->memcached->delete($key);
    }

    public function clear(): bool
    {
        return $this->memcached->flush();
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $keysArray = is_array($keys) ? $keys : iterator_to_array($keys);
        
        if (empty($keysArray)) {
            return [];
        }

        $values = $this->memcached->getMulti($keysArray);
        $results = [];

        foreach ($keysArray as $key) {
            $results[$key] = isset($values[$key]) ? $values[$key] : $default;
        }

        return $results;
    }

    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        $success = true;
        foreach ($values as $key => $value) {
            if (!$this->set($key, $value, $ttl)) {
                $success = false;
            }
        }
        return $success;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $keysArray = is_array($keys) ? $keys : iterator_to_array($keys);
        
        if (empty($keysArray)) {
            return true;
        }

        return $this->memcached->deleteMulti($keysArray) !== false;
    }

    public function has(string $key): bool
    {
        $this->memcached->get($key);
        return $this->memcached->getResultCode() !== Memcached::RES_NOTFOUND;
    }
}
