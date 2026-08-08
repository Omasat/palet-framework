<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow;

use Palet\Framework\Workflow\State\WorkflowState;

class WorkflowInstance
{
    public function __construct(
        public readonly string|int $id,
        public readonly WorkflowDefinition $definition,
        public WorkflowState $state = WorkflowState::DRAFT,
        public ?string $currentStepId = null,
        public array $contextData = []
    ) {}
}
