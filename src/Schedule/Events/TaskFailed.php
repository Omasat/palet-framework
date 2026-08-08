<?php

declare(strict_types=1);

namespace Palet\Framework\Schedule\Events;

use Palet\Framework\Contracts\Schedule\EventInterface;
use Throwable;

class TaskFailed
{
    public readonly EventInterface $event;
    public readonly Throwable $exception;

    public function __construct(EventInterface $event, Throwable $exception)
    {
        $this->event = $event;
        $this->exception = $exception;
    }
}
