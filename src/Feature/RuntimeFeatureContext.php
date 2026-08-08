<?php

declare(strict_types=1);

namespace Palet\Framework\Feature;

class RuntimeFeatureContext
{
    public function __construct(
        public readonly ?string $environment = null,
        public readonly ?string $tenantId = null,
        public readonly ?string $planId = null,
        public readonly ?string $role = null,
        public readonly ?string $userId = null
    ) {}
}
