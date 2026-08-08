<?php

declare(strict_types=1);

namespace Tests\Database\Migrations;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Migrations\MigrationRepository;

class MigrationRepositoryTest extends TestCase
{
    public function test_can_log_and_retrieve_migrations()
    {
        $repository = new MigrationRepository();
        
        $repository->log('2026_01_01_000000_create_users_table', 1);
        $repository->log('2026_01_02_000000_create_posts_table', 2);
        
        $ran = $repository->getRan();
        
        $this->assertCount(2, $ran);
        $this->assertEquals('2026_01_01_000000_create_users_table', $ran[0]);
        $this->assertEquals('2026_01_02_000000_create_posts_table', $ran[1]);
        
        $last = $repository->getLast();
        $this->assertCount(1, $last);
        $this->assertEquals('2026_01_02_000000_create_posts_table', $last[1]['migration']);
    }

    public function test_can_delete_migrations()
    {
        $repository = new MigrationRepository();
        
        $repository->log('2026_01_01_000000_create_users_table', 1);
        $repository->delete('2026_01_01_000000_create_users_table');
        
        $ran = $repository->getRan();
        
        $this->assertEmpty($ran);
    }
}
