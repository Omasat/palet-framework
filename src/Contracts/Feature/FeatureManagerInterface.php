<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Feature;

interface FeatureManagerInterface
{
    public function isActive(string $feature): bool;
    public function enable(string $feature): void;
    public function disable(string $feature): void;
}
