<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Feature;

interface FeaturePolicyInterface
{
    public function evaluate(FeatureFlagInterface $feature, mixed $context): ?bool;
}
