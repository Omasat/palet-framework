<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Provisioning;

use Palet\Framework\Contracts\Tenancy\Provisioning\TenantProvisionerInterface;
use Palet\Framework\Contracts\Tenancy\TenantInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Tenancy\Events\Provisioning\TenantProvisioningStarted;
use Palet\Framework\Tenancy\Events\Provisioning\TenantProvisioned;
use Palet\Framework\Tenancy\Provisioning\State\TenantState;
use RuntimeException;

class TenantProvisioner implements TenantProvisionerInterface
{
    protected TenantProvisionPipeline $pipeline;
    protected TenantLifecycleManager $lifecycle;
    protected ?EventDispatcherInterface $events = null;

    public function __construct(TenantProvisionPipeline $pipeline, TenantLifecycleManager $lifecycle)
    {
        $this->pipeline = $pipeline;
        $this->lifecycle = $lifecycle;
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function provision(array $data): TenantInterface
    {
        if ($this->events) {
            $this->events->dispatch(new TenantProvisioningStarted($data));
        }

        $context = new TenantProvisionContext($data);
        
        $resultContext = $this->pipeline->process($context);

        if (!$resultContext->isSuccess) {
            throw new RuntimeException("Provisioning failed.");
        }

        // Dummy tenant creation for the sake of architecture
        $tenant = new class($resultContext->tenantId, $data) implements TenantInterface {
            private $id;
            private $domain;
            private TenantState $state = TenantState::PENDING;
            
            public function __construct($id, $data) {
                $this->id = $id;
                $this->domain = $data['domain'] ?? 'test.com';
            }
            public function getId(): string|int { return $this->id; }
            public function getDomain(): string { return $this->domain; }
            public function getDatabaseConfig(): array { return []; }
            public function getCachePrefix(): string { return 'tenant_' . $this->id; }
            public function getState(): TenantState { return $this->state; }
            public function setState(TenantState $state): void { $this->state = $state; }
        };

        $this->lifecycle->transitionTo($tenant, TenantState::PROVISIONING);
        // ... build workspace ...
        $this->lifecycle->transitionTo($tenant, TenantState::ACTIVE);

        if ($this->events) {
            $this->events->dispatch(new TenantProvisioned($tenant));
        }

        return $tenant;
    }
}
