<?php

declare(strict_types=1);

namespace Tests\Workflow;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Workflow\WorkflowDefinition;
use Palet\Framework\Workflow\WorkflowInstance;
use Palet\Framework\Workflow\WorkflowEngine;
use Palet\Framework\Workflow\Process\StepManager;
use Palet\Framework\Workflow\Process\TransitionManager;
use Palet\Framework\Workflow\Process\DecisionGateway;
use Palet\Framework\Workflow\Process\ConditionEvaluator;
use Palet\Framework\Workflow\State\WorkflowState;

class WorkflowExecutionTest extends TestCase
{
    public function test_workflow_runs_to_completion()
    {
        $definition = new WorkflowDefinition(1, 'Simple Flow');
        $definition->addStep('step1', ['type' => 'task', 'next' => 'step2'])
                   ->addStep('step2', ['type' => 'task', 'next' => 'step3'])
                   ->addStep('step3', ['type' => 'task', 'next' => null]);
                   
        $instance = new WorkflowInstance(uniqid(), $definition);
        
        $transitionManager = new TransitionManager();
        $gateway = new DecisionGateway(new ConditionEvaluator());
        $stepManager = new StepManager($transitionManager, $gateway);
        $transitionManager->setStepManager($stepManager);
        
        $engine = new WorkflowEngine($stepManager);
        $transitionManager->setEngine($engine);
        
        $engine->start($instance);
        
        $this->assertEquals(WorkflowState::COMPLETED, $instance->state);
        $this->assertNull($instance->currentStepId);
    }
}
