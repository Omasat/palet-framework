<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow\State;

enum WorkflowState: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case WAITING = 'waiting';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';
}
