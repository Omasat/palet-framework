<?php

declare(strict_types=1);

namespace Palet\Framework\Events;

use Psr\EventDispatcher\StoppableEventInterface;

trait StoppableEventTrait
{
    protected bool $propagationStopped = false;

    /**
     * Stop the propagation of the event to further listeners.
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    /**
     * Is propagation stopped?
     *
     * @return bool True if no further listeners should be called.
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
