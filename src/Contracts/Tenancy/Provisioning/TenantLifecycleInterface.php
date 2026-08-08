<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Tenancy\Provisioning;

use Palet\Framework\Contracts\Tenancy\TenantInterface;
use Palet\Framework\Tenancy\Provisioning\State\TenantState;

interface TenantLifecycleInterface
{
    public function transitionTo(TenantInterface $tenant, TenantState $state): void;
    public function canTransitionTo(TenantInterface $tenant, TenantState $state): bool;
}
