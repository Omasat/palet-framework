<?php

declare(strict_types=1);

namespace Tests\Database\Orm\Model;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\Model\ModelCollection;

class ModelCollectionTest extends TestCase
{
    public function test_collection_filters_items()
    {
        $collection = new ModelCollection([1, 2, 3, 4, 5]);
        
        $filtered = $collection->filter(fn($item) => $item > 3);
        
        $this->assertCount(2, $filtered);
        $this->assertEquals(4, $filtered[3]); // keeps original keys
    }
    
    public function test_collection_maps_items()
    {
        $collection = new ModelCollection([1, 2, 3]);
        
        $mapped = $collection->map(fn($item) => $item * 2);
        
        $this->assertEquals(2, $mapped[0]);
        $this->assertEquals(4, $mapped[1]);
        $this->assertEquals(6, $mapped[2]);
    }
    
    public function test_collection_is_arrayable_and_iterable()
    {
        $collection = new ModelCollection(['a' => 1, 'b' => 2]);
        
        $this->assertEquals(1, $collection['a']);
        $collection['c'] = 3;
        $this->assertCount(3, $collection);
        
        $array = $collection->toArray();
        $this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $array);
        
        $json = $collection->toJson();
        $this->assertEquals('{"a":1,"b":2,"c":3}', $json);
    }
}
