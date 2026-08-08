<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Feature;

interface FeatureResolverInterface
{
    public function resolve(FeatureFlagInterface $feature, mixed $context): bool;
}
