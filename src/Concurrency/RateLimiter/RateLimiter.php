<?php

declare(strict_types=1);

namespace Palet\Framework\Concurrency\RateLimiter;

use Palet\Framework\Contracts\Concurrency\RateLimiterInterface;
use Palet\Framework\Contracts\Cache\CacheStoreInterface;

class RateLimiter implements RateLimiterInterface
{
    protected CacheStoreInterface $cache;

    public function __construct(CacheStoreInterface $cache)
    {
        $this->cache = $cache;
    }

    public function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        if ($maxAttempts === 0) {
            return true;
        }

        return $this->attempts($key) >= $maxAttempts;
    }

    public function hit(string $key, int $decaySeconds = 60): int
    {
        $hits = (int) $this->cache->get($key, 0);
        $hits++;
        
        // In a real atomic Redis setup, we would use INCR and EXPIRE.
        $this->cache->set($key, $hits, $decaySeconds);
        $this->cache->set($key . ':timer', time() + $decaySeconds, $decaySeconds);
        
        return $hits;
    }

    public function remaining(string $key, int $maxAttempts): int
    {
        $attempts = $this->attempts($key);
        
        return max(0, $maxAttempts - $attempts);
    }

    public function clear(string $key): void
    {
        $this->cache->delete($key);
        $this->cache->delete($key . ':timer');
    }

    public function availableIn(string $key): int
    {
        $timer = $this->cache->get($key . ':timer');
        
        if ($timer) {
            return max(0, $timer - time());
        }
        
        return 0;
    }

    protected function attempts(string $key): int
    {
        return (int) $this->cache->get($key, 0);
    }
}
