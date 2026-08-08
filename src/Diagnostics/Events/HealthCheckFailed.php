<?php

declare(strict_types=1);

namespace Palet\Framework\Diagnostics\Events;

class HealthCheckFailed
{
    public function __construct(
        public readonly string $checkName,
        public readonly ?string $error
    ) {}
}
