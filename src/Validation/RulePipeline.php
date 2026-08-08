<?php

declare(strict_types=1);

namespace Palet\Framework\Validation;

use Palet\Framework\Contracts\Validation\RuleInterface;

class RulePipeline
{
    protected array $rules = [];

    public function addRule(RuleInterface $rule): static
    {
        $this->rules[] = $rule;
        return $this;
    }

    public function process(string $attribute, mixed $value, MessageBag $errors, bool $bail = false): bool
    {
        $passed = true;
        foreach ($this->rules as $rule) {
            if (!$rule->passes($attribute, $value)) {
                $errors->add($attribute, $rule->message());
                $passed = false;
                
                if ($bail) {
                    break;
                }
            }
        }
        
        return $passed;
    }
}
