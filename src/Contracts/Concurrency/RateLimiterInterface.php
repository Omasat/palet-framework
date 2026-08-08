<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Concurrency;

interface RateLimiterInterface
{
    /**
     * Determine if the given key has been "accessed" too many times.
     */
    public function tooManyAttempts(string $key, int $maxAttempts): bool;

    /**
     * Increment the counter for a given key for a given decay time.
     */
    public function hit(string $key, int $decaySeconds = 60): int;

    /**
     * Get the number of attempts left for the given key.
     */
    public function remaining(string $key, int $maxAttempts): int;

    /**
     * Reset the number of attempts for the given key.
     */
    public function clear(string $key): void;

    /**
     * Get the number of seconds until the key is available again.
     */
    public function availableIn(string $key): int;
}
