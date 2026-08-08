<?php

declare(strict_types=1);

namespace Tests\Validation;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Validation\Rules\RequiredRule;
use Palet\Framework\Validation\Rules\EmailRule;
use Palet\Framework\Validation\Rules\MinRule;
use Palet\Framework\Validation\Rules\MaxRule;

class RulesTest extends TestCase
{
    public function test_required_rule()
    {
        $rule = new RequiredRule();
        
        $this->assertTrue($rule->passes('name', 'John'));
        $this->assertTrue($rule->passes('count', 0)); // 0 is not empty conceptually
        $this->assertTrue($rule->passes('items', [1, 2]));
        
        $this->assertFalse($rule->passes('name', null));
        $this->assertFalse($rule->passes('name', ''));
        $this->assertFalse($rule->passes('name', '   '));
        $this->assertFalse($rule->passes('items', []));
    }
    
    public function test_email_rule()
    {
        $rule = new EmailRule();
        
        $this->assertTrue($rule->passes('email', 'john@example.com'));
        $this->assertTrue($rule->passes('email', '')); // empty passes email (nullable by default)
        
        $this->assertFalse($rule->passes('email', 'invalid-email'));
        $this->assertFalse($rule->passes('email', 'john@'));
    }
    
    public function test_min_rule()
    {
        $rule = new MinRule(5);
        
        // Numeric
        $this->assertTrue($rule->passes('age', 10));
        $this->assertTrue($rule->passes('age', 5));
        $this->assertFalse($rule->passes('age', 4));
        
        // String
        $this->assertTrue($rule->passes('name', 'Johnny')); // len 6
        $this->assertTrue($rule->passes('name', 'Johns')); // len 5
        $this->assertFalse($rule->passes('name', 'John')); // len 4
        
        // Array
        $this->assertTrue($rule->passes('items', [1,2,3,4,5,6]));
        $this->assertFalse($rule->passes('items', [1,2,3]));
    }

    public function test_max_rule()
    {
        $rule = new MaxRule(5);
        
        // Numeric
        $this->assertTrue($rule->passes('age', 4));
        $this->assertTrue($rule->passes('age', 5));
        $this->assertFalse($rule->passes('age', 6));
        
        // String
        $this->assertTrue($rule->passes('name', 'John')); // len 4
        $this->assertTrue($rule->passes('name', 'Johns')); // len 5
        $this->assertFalse($rule->passes('name', 'Johnny')); // len 6
        
        // Array
        $this->assertTrue($rule->passes('items', [1,2,3]));
        $this->assertFalse($rule->passes('items', [1,2,3,4,5,6]));
    }
}
