<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow\Approval;

use Palet\Framework\Workflow\WorkflowInstance;
use Palet\Framework\Workflow\Process\TransitionManager;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Workflow\Events\ApprovalRequested;
use Palet\Framework\Workflow\Events\ApprovalGranted;
use Palet\Framework\Workflow\Events\ApprovalRejected;
use Palet\Framework\Workflow\State\WorkflowState;
use Palet\Framework\Workflow\WorkflowEngine;

class ApprovalManager
{
    public function __construct(
        protected TransitionManager $transitionManager,
        protected WorkflowEngine $engine,
        protected ?EventDispatcherInterface $events = null
    ) {}

    public function requestApproval(WorkflowInstance $instance, string|int $approverId): void
    {
        $instance->state = WorkflowState::WAITING;
        
        if ($this->events) {
            $this->events->dispatch(new ApprovalRequested($instance, $approverId));
        }
    }

    public function approve(WorkflowInstance $instance, string|int $approverId): void
    {
        if ($instance->state !== WorkflowState::WAITING) {
            return;
        }

        if ($this->events) {
            $this->events->dispatch(new ApprovalGranted($instance, $approverId));
        }

        // Move to active and transition
        $instance->state = WorkflowState::ACTIVE;
        
        $stepConfig = $instance->definition->getStep($instance->currentStepId);
        $nextStep = $stepConfig['next'] ?? null;
        
        if ($nextStep) {
            $this->transitionManager->transitionTo($instance, $nextStep);
        } else {
            $this->engine->complete($instance);
        }
    }

    public function reject(WorkflowInstance $instance, string|int $approverId): void
    {
        if ($instance->state !== WorkflowState::WAITING) {
            return;
        }

        if ($this->events) {
            $this->events->dispatch(new ApprovalRejected($instance, $approverId));
        }

        $instance->state = WorkflowState::REJECTED;
        $instance->currentStepId = null;
    }
}
