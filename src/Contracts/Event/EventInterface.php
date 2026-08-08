<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Event;

interface EventInterface
{
    /**
     * Get the name of the event.
     */
    public function getName(): string;
    
    /**
     * Get the payload of the event.
     */
    public function getPayload(): array;
}
