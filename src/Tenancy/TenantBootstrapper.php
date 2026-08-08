<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy;

use Palet\Framework\Contracts\Tenancy\TenantInterface;
use Palet\Framework\Contracts\Tenancy\TenantBootstrapInterface;

class TenantBootstrapper implements TenantBootstrapInterface
{
    // Normally this would inject DatabaseManager, CacheManager, Config etc.
    protected array $originalConfig = [];

    public function bootstrap(TenantInterface $tenant): void
    {
        // Example: Swap DB connections, set cache prefixes.
        // In this implementation we just simulate the environment changes.
        $this->originalConfig['db'] = 'default_db'; // pseudo
    }

    public function revert(): void
    {
        // Revert to original config
        $this->originalConfig = [];
    }
}
