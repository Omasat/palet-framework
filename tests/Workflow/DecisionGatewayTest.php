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

class DecisionGatewayTest extends TestCase
{
    public function test_decision_gateway_routes_correctly()
    {
        $definition = new WorkflowDefinition(1, 'Payment Flow');
        $definition->addStep('check_amount', [
            'type' => 'decision',
            'conditions' => [
                ['condition' => 'is_high_value', 'next' => 'manager_approval'],
                ['condition' => 'default', 'next' => 'auto_approve']
            ]
        ])
        ->addStep('manager_approval', ['type' => 'approval', 'next' => null])
        ->addStep('auto_approve', ['type' => 'task', 'next' => null]);
        
        $evaluator = new ConditionEvaluator();
        $evaluator->addRule('is_high_value', function($context) {
            return ($context['amount'] ?? 0) > 10000;
        });

        $transitionManager = new TransitionManager();
        $gateway = new DecisionGateway($evaluator);
        $stepManager = new StepManager($transitionManager, $gateway);
        $transitionManager->setStepManager($stepManager);
        $engine = new WorkflowEngine($stepManager);
        $transitionManager->setEngine($engine);
        $transitionManager->setEngine($engine);

        // Test High Value Route
        $highValueInstance = new WorkflowInstance(uniqid(), $definition);
        $highValueInstance->contextData = ['amount' => 15000];
        
        $engine->start($highValueInstance);
        
        $this->assertEquals(WorkflowState::WAITING, $highValueInstance->state);
        $this->assertEquals('manager_approval', $highValueInstance->currentStepId);

        // Test Low Value Route
        $lowValueInstance = new WorkflowInstance(uniqid(), $definition);
        $lowValueInstance->contextData = ['amount' => 5000];
        
        $engine->start($lowValueInstance);
        
        $this->assertEquals(WorkflowState::COMPLETED, $lowValueInstance->state);
    }
}
