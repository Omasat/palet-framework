<?php

declare(strict_types=1);

namespace Tests\Support;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Support\Arr;

class ArrTest extends TestCase
{
    public function test_get()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];

        $this->assertEquals(100, Arr::get($array, 'products.desk.price'));
        $this->assertNull(Arr::get($array, 'products.desk.discount'));
        $this->assertEquals('default', Arr::get($array, 'products.macbook', 'default'));
    }

    public function test_set()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::set($array, 'products.desk.price', 200);
        Arr::set($array, 'products.desk.discount', 10);

        $this->assertEquals(200, $array['products']['desk']['price']);
        $this->assertEquals(10, $array['products']['desk']['discount']);
    }

    public function test_has()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];

        $this->assertTrue(Arr::has($array, 'products.desk.price'));
        $this->assertFalse(Arr::has($array, 'products.desk.discount'));
        $this->assertFalse(Arr::has($array, ['products.desk.price', 'products.desk.discount']));
    }
}
