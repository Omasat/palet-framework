<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow;

use Palet\Framework\Workflow\State\WorkflowState;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Workflow\Events\WorkflowStarted;
use Palet\Framework\Workflow\Events\WorkflowCompleted;
use Palet\Framework\Workflow\Process\StepManager;

class WorkflowEngine
{
    public function __construct(
        protected StepManager $stepManager,
        protected ?EventDispatcherInterface $events = null
    ) {}

    public function start(WorkflowInstance $instance): void
    {
        $instance->state = WorkflowState::ACTIVE;
        
        if ($this->events) {
            $this->events->dispatch(new WorkflowStarted($instance));
        }

        // Find initial step if not set
        if ($instance->currentStepId === null) {
            $steps = $instance->definition->getSteps();
            if (!empty($steps)) {
                $instance->currentStepId = array_key_first($steps);
            }
        }

        if ($instance->currentStepId !== null) {
            $this->stepManager->executeStep($instance);
        } else {
            $this->complete($instance);
        }
    }

    public function complete(WorkflowInstance $instance): void
    {
        $instance->state = WorkflowState::COMPLETED;
        $instance->currentStepId = null;

        if ($this->events) {
            $this->events->dispatch(new WorkflowCompleted($instance));
        }
    }
}
