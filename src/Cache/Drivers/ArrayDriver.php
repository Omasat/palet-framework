<?php

declare(strict_types=1);

namespace Palet\Framework\Cache\Drivers;

use Psr\SimpleCache\CacheInterface;

class ArrayDriver implements CacheInterface
{
    protected array $storage = [];
    protected array $expirations = [];

    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->has($key)) {
            return $default;
        }

        return $this->storage[$key];
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $this->storage[$key] = $value;
        
        if ($ttl !== null) {
            $seconds = $ttl instanceof \DateInterval ? (new \DateTime('@0'))->add($ttl)->getTimestamp() : $ttl;
            $this->expirations[$key] = time() + $seconds;
        } else {
            unset($this->expirations[$key]);
        }

        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->storage[$key], $this->expirations[$key]);
        return true;
    }

    public function clear(): bool
    {
        $this->storage = [];
        $this->expirations = [];
        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }
        return $results;
    }

    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    public function has(string $key): bool
    {
        if (!array_key_exists($key, $this->storage)) {
            return false;
        }

        if (isset($this->expirations[$key]) && time() >= $this->expirations[$key]) {
            $this->delete($key);
            return false;
        }

        return true;
    }
}
