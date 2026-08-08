<?php

declare(strict_types=1);

namespace Tests\Tenancy\Provisioning;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Tenancy\Provisioning\TenantLifecycleManager;
use Palet\Framework\Tenancy\Provisioning\State\TenantState;
use Palet\Framework\Contracts\Tenancy\TenantInterface;
use RuntimeException;

class TenantLifecycleTest extends TestCase
{
    protected function createDummyTenant(TenantState $initialState)
    {
        return new class($initialState) implements TenantInterface {
            public TenantState $state;
            public function __construct(TenantState $state) { $this->state = $state; }
            public function getId(): string|int { return 1; }
            public function getDomain(): string { return 'test.com'; }
            public function getDatabaseConfig(): array { return []; }
            public function getCachePrefix(): string { return 'test_'; }
            public function getState(): TenantState { return $this->state; }
            public function setState(TenantState $state): void { $this->state = $state; }
        };
    }

    public function test_valid_transitions()
    {
        $manager = new TenantLifecycleManager();
        $tenant = $this->createDummyTenant(TenantState::PENDING);
        
        $this->assertTrue($manager->canTransitionTo($tenant, TenantState::PROVISIONING));
        $manager->transitionTo($tenant, TenantState::PROVISIONING);
        
        $this->assertEquals(TenantState::PROVISIONING, $tenant->getState());
        
        $manager->transitionTo($tenant, TenantState::ACTIVE);
        $this->assertEquals(TenantState::ACTIVE, $tenant->getState());
    }

    public function test_invalid_transition_throws_exception()
    {
        $manager = new TenantLifecycleManager();
        $tenant = $this->createDummyTenant(TenantState::PENDING);
        
        $this->expectException(RuntimeException::class);
        // Cannot go from Pending directly to Active without Provisioning
        $manager->transitionTo($tenant, TenantState::ACTIVE);
    }
}
