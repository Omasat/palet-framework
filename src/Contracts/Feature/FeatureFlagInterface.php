<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Feature;

interface FeatureFlagInterface
{
    public function getName(): string;
    public function getType(): string;
    public function getState(): string;
    public function getPolicies(): array;
}
