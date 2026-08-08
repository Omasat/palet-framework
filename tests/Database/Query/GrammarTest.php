<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Query\Builder;
use Palet\Framework\Database\Query\Expression;
use Palet\Framework\Database\Query\Grammars\MysqlGrammar;
use Palet\Framework\Database\Query\Grammars\PostgresGrammar;

class GrammarTest extends TestCase
{
    public function test_mysql_compiles_select_query()
    {
        $builder = new Builder(new MysqlGrammar());
        $query = $builder->select(['id', 'name as full_name'])
                         ->from('users')
                         ->where('status', '=', 'active')
                         ->orderBy('created_at', 'desc')
                         ->limit(10)
                         ->offset(5);
                         
        $sql = $query->toSql();
        
        $this->assertEquals('select `id`, `name` as `full_name` from `users` where `status` = ? order by `created_at` desc limit 10 offset 5', $sql);
        $this->assertEquals(['active'], $query->getBindings());
    }

    public function test_postgres_compiles_select_query()
    {
        $builder = new Builder(new PostgresGrammar());
        $query = $builder->select(['id', 'name as full_name'])
                         ->from('users')
                         ->where('status', '=', 'active')
                         ->orderBy('created_at', 'desc')
                         ->limit(10)
                         ->offset(5);
                         
        $sql = $query->toSql();
        
        $this->assertEquals('select "id", "name" as "full_name" from "users" where "status" = ? order by "created_at" desc limit 10 offset 5', $sql);
        $this->assertEquals(['active'], $query->getBindings());
    }
    
    public function test_compiles_insert_statement()
    {
        $grammar = new PostgresGrammar();
        $builder = new Builder($grammar);
        
        $query = $builder->from('users');
        $sql = $grammar->compileInsert($query, ['name' => 'John', 'email' => 'john@test.com']);
        
        $this->assertEquals('insert into "users" ("name", "email") values (?, ?)', $sql);
    }
    
    public function test_compiles_update_statement()
    {
        $grammar = new MysqlGrammar();
        $builder = new Builder($grammar);
        
        $query = $builder->from('users')->where('id', '=', 1);
        $sql = $grammar->compileUpdate($query, ['status' => 'inactive']);
        
        $this->assertEquals('update `users` set `status` = ? where `id` = ?', $sql);
    }
    
    public function test_compiles_delete_statement()
    {
        $grammar = new PostgresGrammar();
        $builder = new Builder($grammar);
        
        $query = $builder->from('users')->where('id', '=', 1);
        $sql = $grammar->compileDelete($query);
        
        $this->assertEquals('delete from "users" where "id" = ?', $sql);
    }
}
