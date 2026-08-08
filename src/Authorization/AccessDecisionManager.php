<?php

declare(strict_types=1);

namespace Palet\Framework\Authorization;

use Palet\Framework\Contracts\Authorization\AuthorizationInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Authorization\Events\AccessGranted;
use Palet\Framework\Authorization\Events\AccessDenied;

class AccessDecisionManager implements AuthorizationInterface
{
    public function __construct(
        protected AuthorizationContext $context,
        protected RoleHierarchy $roleHierarchy,
        protected PolicyEvaluator $policyEvaluator,
        protected array $userRoles = [], // In a real app, this comes from a user repository
        protected array $rolePermissionsMap = [], // Map of role => [permissions...]
        protected ?EventDispatcherInterface $events = null
    ) {}

    public function can(string $ability, mixed $resource = null): bool
    {
        // 1. Policy Evaluation (ABAC & Specific Rules)
        $policyResult = $this->policyEvaluator->evaluate($this->context, $ability, $resource);
        
        if ($policyResult === false) {
            $this->dispatchDenied($ability, $resource);
            return false;
        }

        if ($policyResult === true) {
            $this->dispatchGranted($ability, $resource);
            return true;
        }
        
        // 2. RBAC Evaluation
        $reachableRoles = $this->roleHierarchy->getReachableRoles($this->userRoles);
        
        foreach ($reachableRoles as $role) {
            if (isset($this->rolePermissionsMap[$role]) && in_array($ability, $this->rolePermissionsMap[$role])) {
                $this->dispatchGranted($ability, $resource);
                return true;
            }
        }

        // Default Deny
        $this->dispatchDenied($ability, $resource);
        return false;
    }

    public function cannot(string $ability, mixed $resource = null): bool
    {
        return !$this->can($ability, $resource);
    }
    
    protected function dispatchGranted(string $ability, mixed $resource): void
    {
        if ($this->events) {
            $this->events->dispatch(new AccessGranted($this->context, $ability, $resource));
        }
    }
    
    protected function dispatchDenied(string $ability, mixed $resource): void
    {
        if ($this->events) {
            $this->events->dispatch(new AccessDenied($this->context, $ability, $resource));
        }
    }
}
