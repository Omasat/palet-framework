<?php

declare(strict_types=1);

namespace Palet\Framework\Concurrency\Lock;

use Palet\Framework\Contracts\Cache\CacheStoreInterface;

class LockManager
{
    protected CacheStoreInterface $cache;

    public function __construct(CacheStoreInterface $cache)
    {
        $this->cache = $cache;
    }

    public function lock(string $name, int $seconds = 0, ?string $owner = null): Lock
    {
        return new Lock($this->cache, $name, $seconds, $owner);
    }
}
