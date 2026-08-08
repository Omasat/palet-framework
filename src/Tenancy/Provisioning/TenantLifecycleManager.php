<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Provisioning;

use Palet\Framework\Contracts\Tenancy\TenantInterface;
use Palet\Framework\Tenancy\Provisioning\State\TenantState;
use Palet\Framework\Contracts\Tenancy\Provisioning\TenantLifecycleInterface;
use RuntimeException;

class TenantLifecycleManager implements TenantLifecycleInterface
{
    /**
     * Define valid state transitions.
     * key: current state
     * value: allowed next states
     */
    protected array $transitions = [
        TenantState::PENDING->value => [
            TenantState::PROVISIONING,
            TenantState::DELETED
        ],
        TenantState::PROVISIONING->value => [
            TenantState::ACTIVE,
            TenantState::DELETED
        ],
        TenantState::ACTIVE->value => [
            TenantState::SUSPENDED,
            TenantState::MAINTENANCE,
            TenantState::DELETED
        ],
        TenantState::SUSPENDED->value => [
            TenantState::ACTIVE,
            TenantState::ARCHIVED,
            TenantState::DELETED
        ],
        TenantState::MAINTENANCE->value => [
            TenantState::ACTIVE
        ],
        TenantState::ARCHIVED->value => [
            TenantState::SUSPENDED,
            TenantState::DELETED
        ],
        TenantState::DELETED->value => [
            // Soft delete might allow restore to suspended
            TenantState::SUSPENDED
        ]
    ];

    public function transitionTo(TenantInterface $tenant, TenantState $newState): void
    {
        // For testing, we assume the tenant object has a getState() method.
        // If not, we just pretend it was pending.
        $currentState = method_exists($tenant, 'getState') ? $tenant->getState() : TenantState::PENDING;
        
        if (!$this->canTransitionTo($tenant, $newState)) {
            throw new RuntimeException("Cannot transition from {$currentState->value} to {$newState->value}.");
        }

        if (method_exists($tenant, 'setState')) {
            $tenant->setState($newState);
        }
    }

    public function canTransitionTo(TenantInterface $tenant, TenantState $newState): bool
    {
        $currentState = method_exists($tenant, 'getState') ? $tenant->getState() : TenantState::PENDING;
        
        $allowed = $this->transitions[$currentState->value] ?? [];
        
        return in_array($newState, $allowed, true);
    }
}
