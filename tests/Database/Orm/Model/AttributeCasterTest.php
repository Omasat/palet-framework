<?php

declare(strict_types=1);

namespace Tests\Database\Orm\Model;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\Model\AttributeCaster;
use DateTime;
use DateTimeImmutable;

class AttributeCasterTest extends TestCase
{
    public function test_casts_to_integer()
    {
        $caster = new AttributeCaster();
        $this->assertSame(123, $caster->cast('age', '123', 'int'));
    }
    
    public function test_casts_to_boolean()
    {
        $caster = new AttributeCaster();
        $this->assertTrue($caster->cast('is_active', '1', 'bool'));
        $this->assertFalse($caster->cast('is_active', '0', 'bool'));
    }
    
    public function test_casts_to_array()
    {
        $caster = new AttributeCaster();
        $json = '{"key":"value"}';
        
        $array = $caster->cast('data', $json, 'array');
        
        $this->assertIsArray($array);
        $this->assertEquals('value', $array['key']);
        
        $this->assertEquals($json, $caster->uncast('data', $array, 'array'));
    }
    
    public function test_casts_to_datetime()
    {
        $caster = new AttributeCaster();
        
        $date = clone $caster->cast('created_at', '2026-01-01 12:00:00', 'datetime');
        $this->assertInstanceOf(DateTime::class, $date);
        
        $this->assertEquals('2026-01-01 12:00:00', $caster->uncast('created_at', $date, 'datetime'));
    }
}
