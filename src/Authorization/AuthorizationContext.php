<?php

declare(strict_types=1);

namespace Palet\Framework\Authorization;

class AuthorizationContext
{
    public function __construct(
        public readonly string|int $userId,
        public readonly ?string $tenantId = null,
        public readonly ?string $organizationId = null,
        public readonly ?string $ipAddress = null,
        public readonly ?string $environment = null,
        public readonly ?\DateTimeInterface $timestamp = null
    ) {}
}
