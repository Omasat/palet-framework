<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Schedule;

interface MutexInterface
{
    /**
     * Attempt to obtain an event mutex for the given event.
     */
    public function create(EventInterface $event, \DateTimeInterface $time): bool;

    /**
     * Determine if an event mutex exists for the given event.
     */
    public function exists(EventInterface $event): bool;

    /**
     * Clear the event mutex for the given event.
     */
    public function forget(EventInterface $event): void;
}
