<?php

declare(strict_types=1);

namespace Tests\Database\Repository;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Repository\Specifications\CompositeSpecification;
use stdClass;

class TruthySpec extends CompositeSpecification
{
    public function isSatisfiedBy(object $entity): bool { return true; }
}

class FalsySpec extends CompositeSpecification
{
    public function isSatisfiedBy(object $entity): bool { return false; }
}

class SpecificationTest extends TestCase
{
    public function test_and_specification()
    {
        $truthy = new TruthySpec();
        $falsy = new FalsySpec();
        
        $and1 = $truthy->and($truthy);
        $this->assertTrue($and1->isSatisfiedBy(new stdClass()));
        
        $and2 = $truthy->and($falsy);
        $this->assertFalse($and2->isSatisfiedBy(new stdClass()));
    }

    public function test_or_specification()
    {
        $truthy = new TruthySpec();
        $falsy = new FalsySpec();
        
        $or1 = $falsy->or($truthy);
        $this->assertTrue($or1->isSatisfiedBy(new stdClass()));
        
        $or2 = $falsy->or($falsy);
        $this->assertFalse($or2->isSatisfiedBy(new stdClass()));
    }
    
    public function test_not_specification()
    {
        $truthy = new TruthySpec();
        $falsy = new FalsySpec();
        
        $not1 = $truthy->not();
        $this->assertFalse($not1->isSatisfiedBy(new stdClass()));
        
        $not2 = $falsy->not();
        $this->assertTrue($not2->isSatisfiedBy(new stdClass()));
    }
    
    public function test_complex_composite_specification()
    {
        $t = new TruthySpec();
        $f = new FalsySpec();
        
        // (T OR F) AND (NOT F) => (T) AND (T) => T
        $spec = $t->or($f)->and($f->not());
        
        $this->assertTrue($spec->isSatisfiedBy(new stdClass()));
    }
}
