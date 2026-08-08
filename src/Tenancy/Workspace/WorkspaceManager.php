<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Workspace;

use Palet\Framework\Contracts\Tenancy\Provisioning\WorkspaceManagerInterface;
use Palet\Framework\Contracts\Tenancy\TenantInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Tenancy\Events\Provisioning\WorkspaceCreated;
use Palet\Framework\Tenancy\Events\Provisioning\WorkspaceActivated;
use Palet\Framework\Tenancy\Events\Provisioning\WorkspaceSuspended;
use Palet\Framework\Tenancy\Events\Provisioning\WorkspaceArchived;
use Palet\Framework\Tenancy\Events\Provisioning\WorkspaceDeleted;
use Palet\Framework\Contracts\Tenancy\Provisioning\TenantLifecycleInterface;
use Palet\Framework\Tenancy\Provisioning\State\TenantState;

class WorkspaceManager implements WorkspaceManagerInterface
{
    protected ?EventDispatcherInterface $events = null;
    protected TenantLifecycleInterface $lifecycle;

    public function __construct(TenantLifecycleInterface $lifecycle)
    {
        $this->lifecycle = $lifecycle;
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function create(TenantInterface $tenant): void
    {
        if ($this->events) {
            $this->events->dispatch(new WorkspaceCreated($tenant));
        }
    }

    public function activate(TenantInterface $tenant): void
    {
        $this->lifecycle->transitionTo($tenant, TenantState::ACTIVE);
        if ($this->events) {
            $this->events->dispatch(new WorkspaceActivated($tenant));
        }
    }

    public function suspend(TenantInterface $tenant): void
    {
        $this->lifecycle->transitionTo($tenant, TenantState::SUSPENDED);
        if ($this->events) {
            $this->events->dispatch(new WorkspaceSuspended($tenant));
        }
    }

    public function archive(TenantInterface $tenant): void
    {
        $this->lifecycle->transitionTo($tenant, TenantState::ARCHIVED);
        if ($this->events) {
            $this->events->dispatch(new WorkspaceArchived($tenant));
        }
    }

    public function restore(TenantInterface $tenant): void
    {
        // Typically restores from Archived -> Suspended
        $this->lifecycle->transitionTo($tenant, TenantState::SUSPENDED);
    }

    public function delete(TenantInterface $tenant): void
    {
        $this->lifecycle->transitionTo($tenant, TenantState::DELETED);
        if ($this->events) {
            $this->events->dispatch(new WorkspaceDeleted($tenant));
        }
    }
}
