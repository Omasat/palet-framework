<?php

declare(strict_types=1);

namespace Palet\Framework\Concurrency\Events;

class RateLimitExceeded
{
    public string $key;
    public int $maxAttempts;
    public int $decayMinutes;

    public function __construct(string $key, int $maxAttempts, int $decayMinutes)
    {
        $this->key = $key;
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }
}
