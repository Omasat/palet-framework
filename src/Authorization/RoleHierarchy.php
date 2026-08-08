<?php

declare(strict_types=1);

namespace Palet\Framework\Authorization;

use Palet\Framework\Contracts\Authorization\RoleInterface;

class RoleHierarchy
{
    protected array $hierarchy = [];

    /**
     * Map a parent role to child roles it encompasses.
     */
    public function addInheritance(string $parentRole, string $childRole): void
    {
        $this->hierarchy[$parentRole][] = $childRole;
    }

    /**
     * Get all roles encompassed by the given roles (including themselves).
     *
     * @param array<string> $roles
     * @return array<string>
     */
    public function getReachableRoles(array $roles): array
    {
        $reachable = [];
        
        foreach ($roles as $role) {
            $this->traverseReachable($role, $reachable);
        }
        
        return array_unique($reachable);
    }
    
    protected function traverseReachable(string $role, array &$reachable): void
    {
        $reachable[] = $role;
        
        if (isset($this->hierarchy[$role])) {
            foreach ($this->hierarchy[$role] as $childRole) {
                if (!in_array($childRole, $reachable)) {
                    $this->traverseReachable($childRole, $reachable);
                }
            }
        }
    }
}
