<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Workspace;

use Palet\Framework\Contracts\Tenancy\TenantInterface;

class Workspace
{
    protected TenantInterface $tenant;
    protected WorkspaceManifest $manifest;

    public function __construct(TenantInterface $tenant, WorkspaceManifest $manifest)
    {
        $this->tenant = $tenant;
        $this->manifest = $manifest;
    }

    public function getManifest(): WorkspaceManifest
    {
        return $this->manifest;
    }

    public function getTenant(): TenantInterface
    {
        return $this->tenant;
    }
}
