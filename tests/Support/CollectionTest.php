<?php

declare(strict_types=1);

namespace Tests\Support;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Support\Collection;

class CollectionTest extends TestCase
{
    public function test_map_and_filter()
    {
        $collection = new Collection([1, 2, 3, 4, 5]);

        $result = $collection
            ->map(fn($v) => $v * 2)
            ->filter(fn($v) => $v > 5);

        $this->assertEquals([2 => 6, 3 => 8, 4 => 10], $result->all());
    }

    public function test_reduce()
    {
        $collection = new Collection([1, 2, 3]);
        $sum = $collection->reduce(fn($carry, $v) => $carry + $v, 0);

        $this->assertEquals(6, $sum);
    }

    public function test_first_and_last()
    {
        $collection = new Collection([10, 20, 30]);

        $this->assertEquals(10, $collection->first());
        $this->assertEquals(30, $collection->last());
        $this->assertEquals(20, $collection->first(fn($v) => $v > 10));
    }

    public function test_pluck()
    {
        $collection = new Collection([
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ]);

        $result = $collection->pluck('name', 'id');
        $this->assertEquals([1 => 'Alice', 2 => 'Bob'], $result->all());
    }
}
