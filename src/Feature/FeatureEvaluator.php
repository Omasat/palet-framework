<?php

declare(strict_types=1);

namespace Palet\Framework\Feature;

use Palet\Framework\Contracts\Feature\FeatureFlagInterface;
use Palet\Framework\Contracts\Feature\FeatureResolverInterface;

class FeatureEvaluator implements FeatureResolverInterface
{
    protected array $policies = [];

    public function addPolicy(callable $policy): static
    {
        $this->policies[] = $policy;
        return $this;
    }

    public function resolve(FeatureFlagInterface $feature, mixed $context): bool
    {
        if ($feature->getState() === 'disabled') {
            return false;
        }
        
        if ($feature->getState() === 'draft') {
            return false;
        }

        foreach ($this->policies as $policy) {
            $result = $policy($feature, $context);
            if ($result !== null) {
                return $result;
            }
        }

        return $feature->getState() === 'enabled';
    }
}
