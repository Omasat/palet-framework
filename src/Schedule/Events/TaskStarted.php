<?php

declare(strict_types=1);

namespace Palet\Framework\Schedule\Events;

use Palet\Framework\Contracts\Schedule\EventInterface;

class TaskStarted
{
    public readonly EventInterface $event;

    public function __construct(EventInterface $event)
    {
        $this->event = $event;
    }
}
