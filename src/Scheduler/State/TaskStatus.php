<?php

declare(strict_types=1);

namespace Palet\Framework\Scheduler\State;

enum TaskStatus: string
{
    case SCHEDULED = 'scheduled';
    case WAITING = 'waiting';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case RETRYING = 'retrying';
    case CANCELLED = 'cancelled';
    case DISABLED = 'disabled';
}
