<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Events\Provisioning;

class TenantProvisioningStarted
{
    public function __construct(public readonly array $data) {}
}
