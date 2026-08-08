<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Tenancy\Provisioning;

use Palet\Framework\Contracts\Tenancy\TenantInterface;

interface TenantInitializerInterface
{
    public function initialize(TenantInterface $tenant): void;
}
