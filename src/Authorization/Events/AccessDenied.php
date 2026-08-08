<?php

declare(strict_types=1);

namespace Palet\Framework\Authorization\Events;

use Palet\Framework\Authorization\AuthorizationContext;

class AccessDenied
{
    public function __construct(
        public readonly AuthorizationContext $context,
        public readonly string $ability,
        public readonly mixed $resource = null
    ) {}
}
