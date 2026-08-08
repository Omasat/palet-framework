<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow\Process;

use Palet\Framework\Workflow\WorkflowInstance;

class DecisionGateway
{
    public function __construct(protected ConditionEvaluator $evaluator) {}

    /**
     * Evaluates a set of conditions and returns the next step id
     * 
     * [
     *     ['condition' => 'amount > 1000', 'next' => 'manager_approval'],
     *     ['condition' => 'default', 'next' => 'auto_approve']
     * ]
     */
    public function evaluate(WorkflowInstance $instance, array $conditions): ?string
    {
        foreach ($conditions as $branch) {
            $conditionName = $branch['condition'] ?? 'default';
            
            if ($conditionName === 'default') {
                return $branch['next'] ?? null;
            }
            
            if ($this->evaluator->evaluate($conditionName, $instance->contextData)) {
                return $branch['next'] ?? null;
            }
        }
        
        return null;
    }
}
