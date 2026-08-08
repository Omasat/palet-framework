<?php

declare(strict_types=1);

namespace Palet\Framework\Schedule\Events;

use Palet\Framework\Contracts\Schedule\EventInterface;

class TaskCompleted
{
    public readonly EventInterface $event;
    public readonly float $runtime;

    public function __construct(EventInterface $event, float $runtime)
    {
        $this->event = $event;
        $this->runtime = $runtime;
    }
}
