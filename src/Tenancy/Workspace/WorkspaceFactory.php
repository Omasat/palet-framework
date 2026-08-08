<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Workspace;

use Palet\Framework\Contracts\Tenancy\Provisioning\WorkspaceTemplateInterface;
use Palet\Framework\Contracts\Tenancy\TenantInterface;

class WorkspaceFactory
{
    public function createFromTemplate(TenantInterface $tenant, WorkspaceTemplateInterface $template): Workspace
    {
        $manifestId = uniqid('ws_', true);
        
        $config = $template->getDefaultConfig();
        $resources = $template->getRequiredServices();
        
        $manifest = new WorkspaceManifest(
            workspaceId: $manifestId,
            tenantId: (string) $tenant->getId(),
            config: $config,
            resources: $resources
        );

        return new Workspace($tenant, $manifest);
    }
}
