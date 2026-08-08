<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow\Process;

use Palet\Framework\Workflow\WorkflowInstance;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Workflow\Events\StepEntered;

class StepManager
{
    public function __construct(
        protected TransitionManager $transitionManager,
        protected DecisionGateway $gateway,
        protected ?EventDispatcherInterface $events = null
    ) {}

    public function executeStep(WorkflowInstance $instance): void
    {
        $stepId = $instance->currentStepId;
        $stepConfig = $instance->definition->getStep($stepId);

        if (!$stepConfig) {
            return; // invalid step
        }

        if ($this->events) {
            $this->events->dispatch(new StepEntered($instance, $stepId));
        }

        $type = $stepConfig['type'] ?? 'task';

        if ($type === 'decision') {
            $nextStep = $this->gateway->evaluate($instance, $stepConfig['conditions']);
            $this->transitionManager->transitionTo($instance, $nextStep);
        } elseif ($type === 'approval') {
            // Suspends execution and waits for external trigger
            $instance->state = \Palet\Framework\Workflow\State\WorkflowState::WAITING;
        } else {
            // Normal task, just move to next if defined
            $nextStep = $stepConfig['next'] ?? null;
            $this->transitionManager->transitionTo($instance, $nextStep);
        }
    }
}
