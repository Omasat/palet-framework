<?php

declare(strict_types=1);

namespace Tests\Tenancy\Provisioning;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Tenancy\Workspace\WorkspaceManager;
use Palet\Framework\Tenancy\Provisioning\TenantLifecycleManager;
use Palet\Framework\Tenancy\Provisioning\State\TenantState;
use Palet\Framework\Contracts\Tenancy\TenantInterface;

class WorkspaceManagerTest extends TestCase
{
    public function test_workspace_manager_changes_state()
    {
        $lifecycle = new TenantLifecycleManager();
        $manager = new WorkspaceManager($lifecycle);
        
        $tenant = new class() implements TenantInterface {
            public TenantState $state = TenantState::ACTIVE;
            public function getId(): string|int { return 1; }
            public function getDomain(): string { return 'test.com'; }
            public function getDatabaseConfig(): array { return []; }
            public function getCachePrefix(): string { return 'test_'; }
            public function getState(): TenantState { return $this->state; }
            public function setState(TenantState $state): void { $this->state = $state; }
        };
        
        // Active -> Suspended
        $manager->suspend($tenant);
        $this->assertEquals(TenantState::SUSPENDED, $tenant->getState());
        
        // Suspended -> Archived
        $manager->archive($tenant);
        $this->assertEquals(TenantState::ARCHIVED, $tenant->getState());
        
        // Archived -> Deleted
        $manager->delete($tenant);
        $this->assertEquals(TenantState::DELETED, $tenant->getState());
    }
}
