<?php

declare(strict_types=1);

namespace Tests\Tenancy;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Tenancy\TenantBootstrapper;
use Palet\Framework\Contracts\Tenancy\TenantInterface;

class TenantBootstrapperTest extends TestCase
{
    public function test_bootstrapper_can_bootstrap_and_revert()
    {
        $bootstrapper = new TenantBootstrapper();
        
        $tenant = new class implements TenantInterface {
            public function getId(): string|int { return '456'; }
            public function getDomain(): string { return 'test.com'; }
            public function getDatabaseConfig(): array { return []; }
            public function getCachePrefix(): string { return 'tenant456_'; }
        };
        
        // Ensure no exception is thrown during bootstrap/revert
        $bootstrapper->bootstrap($tenant);
        $bootstrapper->revert();
        
        $this->assertTrue(true);
    }
}
