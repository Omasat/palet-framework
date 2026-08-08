<?php

declare(strict_types=1);

namespace Palet\Framework\Concurrency\Events;

class LockAcquired
{
    public string $name;
    public string $owner;
    public int $seconds;

    public function __construct(string $name, string $owner, int $seconds)
    {
        $this->name = $name;
        $this->owner = $owner;
        $this->seconds = $seconds;
    }
}
