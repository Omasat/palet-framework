<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Events;

use Palet\Framework\Contracts\Tenancy\TenantInterface;

class TenantBootstrapped
{
    public function __construct(public readonly TenantInterface $tenant) {}
}
