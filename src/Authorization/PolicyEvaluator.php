<?php

declare(strict_types=1);

namespace Palet\Framework\Authorization;

use Palet\Framework\Contracts\Authorization\PolicyInterface;

class PolicyEvaluator
{
    protected array $policies = [];

    public function addPolicy(PolicyInterface $policy): void
    {
        $this->policies[] = $policy;
    }

    /**
     * Evaluates all policies in chain.
     * Returns true if explicitly allowed.
     * Returns false if explicitly denied.
     * Returns null if no policy matched.
     */
    public function evaluate(mixed $context, string $ability, mixed $resource = null): ?bool
    {
        foreach ($this->policies as $policy) {
            $result = $policy->evaluate($context, $ability, $resource);
            if ($result !== null) {
                return $result; // explicit allow or deny
            }
        }
        return null;
    }
}
