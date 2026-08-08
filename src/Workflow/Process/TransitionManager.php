<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow\Process;

use Palet\Framework\Workflow\WorkflowInstance;
use Palet\Framework\Workflow\WorkflowEngine;

class TransitionManager
{
    protected ?StepManager $stepManager = null;
    protected ?WorkflowEngine $engine = null;

    public function setStepManager(StepManager $stepManager): void
    {
        $this->stepManager = $stepManager;
    }

    public function setEngine(WorkflowEngine $engine): void
    {
        $this->engine = $engine;
    }

    public function transitionTo(WorkflowInstance $instance, ?string $nextStepId): void
    {
        $instance->currentStepId = $nextStepId;
        
        if ($nextStepId === null) {
            if ($this->engine) {
                $this->engine->complete($instance);
            }
            return;
        }

        if ($this->stepManager) {
            $this->stepManager->executeStep($instance);
        }
    }
}
