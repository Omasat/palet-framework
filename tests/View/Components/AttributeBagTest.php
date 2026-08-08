<?php

declare(strict_types=1);

namespace Tests\View\Components;

use PHPUnit\Framework\TestCase;
use Palet\Framework\View\Components\AttributeBag;

class AttributeBagTest extends TestCase
{
    public function test_get_and_has()
    {
        $bag = new AttributeBag(['id' => 'alert', 'class' => 'bg-red']);
        
        $this->assertTrue($bag->has('id'));
        $this->assertFalse($bag->has('style'));
        
        $this->assertEquals('alert', $bag->get('id'));
        $this->assertNull($bag->get('style'));
        $this->assertEquals('fallback', $bag->get('style', 'fallback'));
    }

    public function test_merge()
    {
        $bag = new AttributeBag(['id' => 'alert', 'class' => 'text-white']);
        
        $merged = $bag->merge(['class' => 'bg-red p-4', 'data-type' => 'error']);
        
        $this->assertEquals('text-white bg-red p-4', $merged->get('class'));
        $this->assertEquals('error', $merged->get('data-type'));
        $this->assertEquals('alert', $merged->get('id'));
    }

    public function test_to_string()
    {
        $bag = new AttributeBag(['id' => 'alert', 'class' => 'bg-red', 'required' => true, 'disabled' => false]);
        
        $html = (string) $bag;
        
        $this->assertEquals('id="alert" class="bg-red" required', $html);
    }
}
