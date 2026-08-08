<?php

declare(strict_types=1);

namespace Tests\Database\Repository;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Contracts\Database\Repository\CriteriaInterface;
use Palet\Framework\Database\Repository\CriteriaPipeline;
use stdClass;

class MockCriteria implements CriteriaInterface
{
    public function apply(mixed $query): mixed
    {
        $query->whereApplied = true;
        return $query;
    }
}

class CriteriaPipelineTest extends TestCase
{
    public function test_pipeline_applies_criteria()
    {
        $pipeline = new CriteriaPipeline();
        $pipeline->push(new MockCriteria());
        
        $query = new stdClass();
        $query->whereApplied = false;
        
        $result = $pipeline->apply($query);
        
        $this->assertTrue($result->whereApplied);
    }
    
    public function test_pipeline_can_be_cleared()
    {
        $pipeline = new CriteriaPipeline();
        $pipeline->push(new MockCriteria());
        
        $pipeline->clear();
        
        $query = new stdClass();
        $query->whereApplied = false;
        
        $result = $pipeline->apply($query);
        
        $this->assertFalse($result->whereApplied);
    }
}
