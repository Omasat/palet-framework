<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow\Events;

use Palet\Framework\Workflow\WorkflowInstance;

class WorkflowStarted
{
    public function __construct(public readonly WorkflowInstance $instance) {}
}
