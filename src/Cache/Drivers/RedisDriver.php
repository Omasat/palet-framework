<?php

declare(strict_types=1);

namespace Palet\Framework\Cache\Drivers;

use Psr\SimpleCache\CacheInterface;
use Redis;

class RedisDriver implements CacheInterface
{
    protected Redis $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->redis->get($key);

        if ($value === false) {
            return $default;
        }

        return is_numeric($value) ? $value : unserialize($value);
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $value = is_numeric($value) ? $value : serialize($value);

        if ($ttl === null) {
            return $this->redis->set($key, $value);
        }

        $seconds = $ttl instanceof \DateInterval ? (new \DateTime('@0'))->add($ttl)->getTimestamp() : $ttl;

        if ($seconds <= 0) {
            return $this->delete($key);
        }

        return $this->redis->setex($key, $seconds, $value);
    }

    public function delete(string $key): bool
    {
        return (bool) $this->redis->del($key);
    }

    public function clear(): bool
    {
        return $this->redis->flushDB();
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $keysArray = is_array($keys) ? $keys : iterator_to_array($keys);
        
        if (empty($keysArray)) {
            return [];
        }

        $values = $this->redis->mGet($keysArray);
        $results = [];

        foreach ($keysArray as $index => $key) {
            $value = $values[$index];
            $results[$key] = $value !== false ? (is_numeric($value) ? $value : unserialize($value)) : $default;
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

        $this->redis->del($keysArray);
        return true;
    }

    public function has(string $key): bool
    {
        return (bool) $this->redis->exists($key);
    }
}
