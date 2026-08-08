<?php

declare(strict_types=1);

namespace Tests\Database\Migrations\Generator;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Migrations\Generator\MigrationNameResolver;

class MigrationNameResolverTest extends TestCase
{
    public function test_resolves_create_table()
    {
        $resolver = new MigrationNameResolver();
        
        $analysis = $resolver->analyze('create_users_table');
        
        $this->assertEquals('users', $analysis['table']);
        $this->assertTrue($analysis['create']);
    }

    public function test_resolves_alter_table()
    {
        $resolver = new MigrationNameResolver();
        
        $analysis = $resolver->analyze('add_votes_to_posts_table');
        
        $this->assertEquals('posts', $analysis['table']);
        $this->assertFalse($analysis['create']);
    }

    public function test_resolves_drop_table()
    {
        $resolver = new MigrationNameResolver();
        
        $analysis = $resolver->analyze('drop_sessions_table');
        
        $this->assertEquals('sessions', $analysis['table']);
        $this->assertFalse($analysis['create']);
    }

    public function test_returns_null_for_unrecognized_pattern()
    {
        $resolver = new MigrationNameResolver();
        
        $analysis = $resolver->analyze('some_random_migration_name');
        
        $this->assertNull($analysis['table']);
        $this->assertFalse($analysis['create']);
    }
}
