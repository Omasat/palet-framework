<?php

declare(strict_types=1);

namespace Palet\Framework\Concurrency\Events;

class LockReleased
{
    public string $name;
    public string $owner;

    public function __construct(string $name, string $owner)
    {
        $this->name = $name;
        $this->owner = $owner;
    }
}
