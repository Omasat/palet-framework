<?php

declare(strict_types=1);

namespace Tests\Authorization;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Authorization\RoleHierarchy;

class RbacAuthorizationTest extends TestCase
{
    public function test_role_hierarchy_resolves_reachable_roles()
    {
        $hierarchy = new RoleHierarchy();
        
        // Admin inherits Manager, Manager inherits User
        $hierarchy->addInheritance('admin', 'manager');
        $hierarchy->addInheritance('manager', 'user');
        
        $reachableFromAdmin = $hierarchy->getReachableRoles(['admin']);
        
        $this->assertContains('admin', $reachableFromAdmin);
        $this->assertContains('manager', $reachableFromAdmin);
        $this->assertContains('user', $reachableFromAdmin);
        $this->assertCount(3, $reachableFromAdmin);
        
        $reachableFromManager = $hierarchy->getReachableRoles(['manager']);
        $this->assertNotContains('admin', $reachableFromManager);
        $this->assertContains('manager', $reachableFromManager);
        $this->assertContains('user', $reachableFromManager);
    }
}
