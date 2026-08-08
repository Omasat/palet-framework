<?php

declare(strict_types=1);

namespace Palet\Framework\Schedule;

use Palet\Framework\Contracts\Schedule\MutexInterface;
use Palet\Framework\Contracts\Schedule\EventInterface;
use Palet\Framework\Contracts\Cache\CacheStoreInterface as Cache;

class Mutex implements MutexInterface
{
    protected Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function create(EventInterface $event, \DateTimeInterface $time): bool
    {
        // Simple mock lock logic.
        $key = $this->getMutexName($event);
        
        // In real world, we would use atomic add/setnx on cache.
        if ($this->cache->has($key)) {
            return false;
        }

        // Set lock for 1440 minutes (1 day) by default
        $this->cache->set($key, true, 1440 * 60);
        
        return true;
    }

    public function exists(EventInterface $event): bool
    {
        return $this->cache->has($this->getMutexName($event));
    }

    public function forget(EventInterface $event): void
    {
        $this->cache->delete($this->getMutexName($event));
    }

    protected function getMutexName(EventInterface $event): string
    {
        return 'framework/schedule-' . sha1(serialize($event->command) . serialize($event->parameters) . $event->expression);
    }
}
