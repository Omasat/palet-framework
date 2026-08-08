<?php

declare(strict_types=1);

namespace Tests\Database\Schema;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Schema\Blueprint;
use Palet\Framework\Database\Schema\Grammars\MysqlGrammar;
use Palet\Framework\Database\Schema\Grammars\PostgresGrammar;
use Palet\Framework\Database\Schema\Grammars\SqliteGrammar;

class SchemaGrammarTest extends TestCase
{
    public function test_mysql_grammar_compiles_create_table()
    {
        $blueprint = new Blueprint('users');
        $blueprint->create();
        $blueprint->id();
        $blueprint->string('email')->unique();
        $blueprint->string('name')->nullable();
        
        $grammar = new MysqlGrammar();
        $statements = $blueprint->build($grammar);
        
        $this->assertCount(1, $statements);
        $this->assertEquals('create table `users` (`id` bigint unsigned not null auto_increment primary key, `email` varchar(255) not null, `name` varchar(255) null) default character set utf8mb4', $statements[0]);
    }

    public function test_postgres_grammar_compiles_create_table()
    {
        $blueprint = new Blueprint('users');
        $blueprint->create();
        $blueprint->id();
        $blueprint->string('email')->unique();
        $blueprint->string('name')->nullable();
        
        $grammar = new PostgresGrammar();
        $statements = $blueprint->build($grammar);
        
        $this->assertCount(1, $statements);
        $this->assertEquals('create table "users" ("id" bigserial not null primary key, "email" varchar(255) not null, "name" varchar(255) null)', $statements[0]);
    }
    
    public function test_sqlite_grammar_compiles_create_table()
    {
        $blueprint = new Blueprint('users');
        $blueprint->create();
        $blueprint->id();
        $blueprint->string('email')->unique();
        $blueprint->string('name')->nullable();
        
        $grammar = new SqliteGrammar();
        $statements = $blueprint->build($grammar);
        
        $this->assertCount(1, $statements);
        $this->assertEquals('create table "users" ("id" integer not null primary key autoincrement, "email" varchar not null, "name" varchar null)', $statements[0]);
    }
}
