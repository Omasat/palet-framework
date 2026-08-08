<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Tenancy;

interface TenantBootstrapInterface
{
    /**
     * Bootstraps the application environment for the given tenant.
     * (e.g., configures database connection, cache prefixes).
     */
    public function bootstrap(TenantInterface $tenant): void;
    
    /**
     * Reverts the application environment to the default state.
     */
    public function revert(): void;
}
