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
use Palet\Framework\Workflow\Approval\ApprovalManager;
use Palet\Framework\Workflow\State\WorkflowState;

class ApprovalFlowTest extends TestCase
{
    public function test_workflow_suspends_for_approval_and_resumes()
    {
        $definition = new WorkflowDefinition(1, 'Approval Flow');
        $definition->addStep('init', ['type' => 'task', 'next' => 'approve_doc'])
                   ->addStep('approve_doc', ['type' => 'approval', 'next' => 'publish'])
                   ->addStep('publish', ['type' => 'task', 'next' => null]);
                   
        $instance = new WorkflowInstance(uniqid(), $definition);
        
        $transitionManager = new TransitionManager();
        $gateway = new DecisionGateway(new ConditionEvaluator());
        $stepManager = new StepManager($transitionManager, $gateway);
        $transitionManager->setStepManager($stepManager);
        
        $engine = new WorkflowEngine($stepManager);
        $transitionManager->setEngine($engine);
        $approvalManager = new ApprovalManager($transitionManager, $engine);
        
        $engine->start($instance);
        
        // At this point, it should have hit "approve_doc" and stopped
        $this->assertEquals(WorkflowState::WAITING, $instance->state);
        $this->assertEquals('approve_doc', $instance->currentStepId);
        
        // Simulate human approval
        $approvalManager->approve($instance, 99);
        
        // Now it should have continued to publish and finished
        $this->assertEquals(WorkflowState::COMPLETED, $instance->state);
    }

    public function test_workflow_rejection()
    {
        $definition = new WorkflowDefinition(1, 'Approval Flow');
        $definition->addStep('approve_doc', ['type' => 'approval', 'next' => 'publish']);
                   
        $instance = new WorkflowInstance(uniqid(), $definition);
        
        $transitionManager = new TransitionManager();
        $gateway = new DecisionGateway(new ConditionEvaluator());
        $stepManager = new StepManager($transitionManager, $gateway);
        $transitionManager->setStepManager($stepManager);
        
        $engine = new WorkflowEngine($stepManager);
        $transitionManager->setEngine($engine);
        $approvalManager = new ApprovalManager($transitionManager, $engine);
        
        $engine->start($instance);
        
        $this->assertEquals(WorkflowState::WAITING, $instance->state);
        
        // Simulate human rejection
        $approvalManager->reject($instance, 99);
        
        $this->assertEquals(WorkflowState::REJECTED, $instance->state);
        $this->assertNull($instance->currentStepId);
    }
}
