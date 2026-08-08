<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy;

use Palet\Framework\Contracts\Tenancy\TenantInterface;

class TenantLocator
{
    /**
     * @var TenantInterface[]
     */
    protected array $tenants = [];

    public function register(TenantInterface $tenant): void
    {
        $this->tenants[$tenant->getId()] = $tenant;
    }

    public function findById(string|int $id): ?TenantInterface
    {
        return $this->tenants[$id] ?? null;
    }
}
