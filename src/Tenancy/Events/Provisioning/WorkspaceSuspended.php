<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Events\Provisioning;

use Palet\Framework\Contracts\Tenancy\TenantInterface;

class WorkspaceSuspended
{
    public function __construct(public readonly TenantInterface $tenant) {}
}
