<?php

declare(strict_types=1);

namespace Tests\Database\Orm\Advanced;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\Scope\GlobalScopeManager;
use Palet\Framework\Database\Orm\Scope\SoftDeleteScope;
use stdClass;

class MockQueryBuilder
{
    public bool $softDeleteApplied = false;
}

class GlobalScopeTest extends TestCase
{
    public function test_manager_applies_scopes()
    {
        $manager = new GlobalScopeManager();
        $manager->addScope('UserModel', 'softDelete', new SoftDeleteScope());
        
        $query = new MockQueryBuilder();
        $manager->applyScopes('UserModel', $query, new stdClass());
        
        $this->assertTrue($query->softDeleteApplied);
    }
}
