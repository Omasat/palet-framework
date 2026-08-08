<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Tenancy\Provisioning;

use Palet\Framework\Contracts\Tenancy\TenantInterface;

interface TenantProvisionerInterface
{
    /**
     * Start the provisioning process for a new tenant.
     * 
     * @param array $data Tenant configuration data
     * @return TenantInterface The newly provisioned tenant
     */
    public function provision(array $data): TenantInterface;
}
