<?php

declare(strict_types=1);

namespace Palet\Framework\Queue\State;

enum JobStatus: string
{
    case PENDING = 'pending';
    case QUEUED = 'queued';
    case RESERVED = 'reserved';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case RETRYING = 'retrying';
    case CANCELLED = 'cancelled';
}
