<?php

declare(strict_types=1);

namespace Tests\Organization;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Organization\OrganizationHierarchy;
use Palet\Framework\Organization\Models\OrganizationNode;
use Palet\Framework\Organization\Models\BranchNode;
use Palet\Framework\Organization\Models\DepartmentNode;
use Palet\Framework\Organization\Models\TeamNode;

class RecursiveTraversalTest extends TestCase
{
    public function test_get_descendants_finds_all_children()
    {
        $org = new OrganizationNode(1, 'Holding');
        
        $branch1 = new BranchNode(2, 'Istanbul');
        $branch2 = new BranchNode(3, 'Ankara');
        
        $dept1 = new DepartmentNode(4, 'IT');
        $team1 = new TeamNode(5, 'Backend');
        
        $org->addChild($branch1);
        $org->addChild($branch2);
        
        $branch1->addChild($dept1);
        $dept1->addChild($team1);
        
        $hierarchy = new OrganizationHierarchy();
        
        $descendants = $hierarchy->getDescendants($org);
        
        $this->assertCount(4, $descendants); // branch1, dept1, team1, branch2
    }

    public function test_get_path_finds_correct_route()
    {
        $org = new OrganizationNode(1, 'Holding');
        $branch1 = new BranchNode(2, 'Istanbul');
        $dept1 = new DepartmentNode(4, 'IT');
        $team1 = new TeamNode(5, 'Backend');
        
        $org->addChild($branch1);
        $branch1->addChild($dept1);
        $dept1->addChild($team1);
        
        $hierarchy = new OrganizationHierarchy();
        
        $path = $hierarchy->getPath($team1);
        
        $this->assertCount(4, $path);
        $this->assertSame($org, $path[0]);
        $this->assertSame($team1, $path[3]);
    }
}
