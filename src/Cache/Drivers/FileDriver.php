<?php

declare(strict_types=1);

namespace Palet\Framework\Cache\Drivers;

use Psr\SimpleCache\CacheInterface;

class FileDriver implements CacheInterface
{
    protected string $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0777, true);
        }
    }

    protected function getPath(string $key): string
    {
        $hash = md5($key);
        return $this->directory . DIRECTORY_SEPARATOR . $hash;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $path = $this->getPath($key);

        if (!file_exists($path)) {
            return $default;
        }

        $contents = file_get_contents($path);
        
        // Simple serialization with expiration check: EXPIRE_TIMESTAMP|SERIALIZED_DATA
        $parts = explode('|', $contents, 2);
        
        if (count($parts) !== 2) {
            $this->delete($key);
            return $default;
        }

        $expire = (int) $parts[0];
        
        if ($expire !== 0 && time() >= $expire) {
            $this->delete($key);
            return $default;
        }

        return unserialize($parts[1]);
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $path = $this->getPath($key);
        
        $expire = 0;
        if ($ttl !== null) {
            $seconds = $ttl instanceof \DateInterval ? (new \DateTime('@0'))->add($ttl)->getTimestamp() : $ttl;
            $expire = time() + $seconds;
        }

        $data = $expire . '|' . serialize($value);

        return file_put_contents($path, $data, LOCK_EX) !== false;
    }

    public function delete(string $key): bool
    {
        $path = $this->getPath($key);
        if (file_exists($path)) {
            return unlink($path);
        }
        return true;
    }

    public function clear(): bool
    {
        $files = glob($this->directory . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
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
        $path = $this->getPath($key);
        if (!file_exists($path)) {
            return false;
        }
        
        // Check expiration
        $contents = file_get_contents($path);
        $parts = explode('|', $contents, 2);
        
        if (count($parts) !== 2) {
            return false;
        }
        
        $expire = (int) $parts[0];
        
        if ($expire !== 0 && time() >= $expire) {
            $this->delete($key);
            return false;
        }
        
        return true;
    }
}
