<?php

declare(strict_types=1);

namespace Tests\Tenancy;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Tenancy\TenantManager;
use Palet\Framework\Tenancy\TenantContext;
use Palet\Framework\Tenancy\TenantLocator;
use Palet\Framework\Tenancy\TenantBootstrapper;
use Palet\Framework\Tenancy\Resolution\HeaderTenantResolver;
use Palet\Framework\Contracts\Tenancy\TenantInterface;
use Palet\Framework\Http\Message\Request;
use RuntimeException;

class TenantManagerTest extends TestCase
{
    public function test_initializes_tenant_context()
    {
        $context = new TenantContext();
        $locator = new TenantLocator();
        $bootstrapper = new TenantBootstrapper();
        
        $tenant = new class implements TenantInterface {
            public function getId(): string|int { return '123'; }
            public function getDomain(): string { return 'test.com'; }
            public function getDatabaseConfig(): array { return []; }
            public function getCachePrefix(): string { return 'tenant123_'; }
        };
        $locator->register($tenant);
        
        $manager = new TenantManager($context, $locator, $bootstrapper);
        $manager->addResolver(new HeaderTenantResolver('X-Tenant'));
        
        $request = (new Request('GET', '/'))->withHeader('X-Tenant', '123');
        
        $manager->initialize($request);
        
        $this->assertTrue($context->hasTenant());
        $this->assertEquals('123', $context->getTenant()->getId());
        
        $manager->endContext();
        $this->assertFalse($context->hasTenant());
    }

    public function test_throws_when_tenant_not_resolved()
    {
        $context = new TenantContext();
        $locator = new TenantLocator();
        $bootstrapper = new TenantBootstrapper();
        
        $manager = new TenantManager($context, $locator, $bootstrapper);
        $manager->addResolver(new HeaderTenantResolver('X-Tenant'));
        
        // Request without header
        $request = new Request('GET', '/');
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not resolve tenant from request.');
        $manager->initialize($request);
    }
}
