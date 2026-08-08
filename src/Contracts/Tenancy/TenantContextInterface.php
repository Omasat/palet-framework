<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Tenancy;

interface TenantContextInterface
{
    public function getTenant(): ?TenantInterface;
    public function setTenant(?TenantInterface $tenant): void;
    public function hasTenant(): bool;
}
