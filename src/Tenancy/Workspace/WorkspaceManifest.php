<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Workspace;

use Palet\Framework\Contracts\Tenancy\TenantInterface;

class WorkspaceManifest
{
    public function __construct(
        public readonly string $workspaceId,
        public readonly string $tenantId,
        public readonly array $config,
        public readonly array $resources = []
    ) {}
}
