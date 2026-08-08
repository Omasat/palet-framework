<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Event;

interface ListenerInterface
{
    /**
     * Handle the event.
     */
    public function handle(EventInterface $event): void;
}
