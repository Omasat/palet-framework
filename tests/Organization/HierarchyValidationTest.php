<?php

declare(strict_types=1);

namespace Tests\Organization;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Organization\Models\OrganizationNode;
use Palet\Framework\Organization\Models\DepartmentNode;
use Palet\Framework\Organization\Exceptions\CircularHierarchyException;

class HierarchyValidationTest extends TestCase
{
    public function test_circular_dependency_throws_exception()
    {
        $org = new OrganizationNode(1, 'Headquarters');
        $deptA = new DepartmentNode(2, 'Engineering');
        $deptB = new DepartmentNode(3, 'QA');

        $org->addChild($deptA);
        $deptA->addChild($deptB);

        // Try to add Org under QA, which should fail
        $this->expectException(CircularHierarchyException::class);
        $deptB->addChild($org);
    }
    
    public function test_valid_hierarchy_does_not_throw()
    {
        $org = new OrganizationNode(1, 'Headquarters');
        $deptA = new DepartmentNode(2, 'Engineering');
        
        $org->addChild($deptA);
        
        $this->assertSame($org, $deptA->getParent());
        $this->assertCount(1, $org->getChildren());
    }
}
