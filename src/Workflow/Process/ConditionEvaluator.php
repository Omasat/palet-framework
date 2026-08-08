<?php

declare(strict_types=1);

namespace Palet\Framework\Workflow\Process;

class ConditionEvaluator
{
    protected array $rules = [];

    public function addRule(string $name, callable $evaluator): void
    {
        $this->rules[$name] = $evaluator;
    }

    public function evaluate(string $conditionName, array $contextData): bool
    {
        if (!isset($this->rules[$conditionName])) {
            return false;
        }

        $evaluator = $this->rules[$conditionName];
        return $evaluator($contextData);
    }
}
