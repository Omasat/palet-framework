<?php

declare(strict_types=1);

namespace Palet\Framework\Feature\Events;

use Palet\Framework\Contracts\Feature\FeatureFlagInterface;

class FeatureDisabled
{
    public function __construct(public readonly FeatureFlagInterface $feature) {}
}
