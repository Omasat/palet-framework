<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow\Events;

use Palet\Framework\Workflow\WorkflowInstance;

class WorkflowCancelled
{
    public function __construct(public readonly WorkflowInstance $instance) {}
}
