<?php

declare(strict_types=1);

namespace Palet\Framework\Diagnostics\Events;

class HealthCheckPassed
{
    public function __construct(public readonly string $checkName) {}
}
