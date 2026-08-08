<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy;

use Palet\Framework\Contracts\Tenancy\TenantContextInterface;
use Palet\Framework\Contracts\Tenancy\TenantInterface;

class TenantContext implements TenantContextInterface
{
    protected ?TenantInterface $tenant = null;

    public function getTenant(): ?TenantInterface
    {
        return $this->tenant;
    }

    public function setTenant(?TenantInterface $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }
}
