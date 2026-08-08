<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Query\Builder;
use Palet\Framework\Database\Query\Grammars\PostgresGrammar;

class QueryBuilderTest extends TestCase
{
    public function test_builder_is_immutable()
    {
        $builder = new Builder();
        
        $newBuilder = $builder->where('id', '=', 1);
        
        $this->assertNotSame($builder, $newBuilder);
        $this->assertEmpty($builder->wheres);
        $this->assertCount(1, $newBuilder->wheres);
    }
    
    public function test_nested_where_adds_bindings_correctly()
    {
        $builder = new Builder(new PostgresGrammar());
        
        $query = $builder->from('users')->where(function($q) {
            return $q->where('id', '=', 1)->orWhere('status', '=', 'active');
        });
        
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        
        $this->assertEquals('select * from "users" where ("id" = ? or "status" = ?)', $sql);
        $this->assertEquals([1, 'active'], $bindings);
    }
}
