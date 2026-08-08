<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Concurrency;

interface LockInterface
{
    /**
     * Attempt to acquire the lock.
     */
    public function acquire(): bool;

    /**
     * Release the lock.
     */
    public function release(): bool;

    /**
     * Release the lock regardless of ownership.
     */
    public function forceRelease(): void;

    /**
     * Returns the current owner of the lock.
     */
    public function owner(): string;
    
    /**
     * Returns the lock name.
     */
    public function name(): string;
}
