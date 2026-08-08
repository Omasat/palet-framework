<?php

declare(strict_types=1);

namespace Tests\Database\Migrations;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Migrations\MigrationLoader;

class MigrationLoaderTest extends TestCase
{
    protected string $path;

    protected function setUp(): void
    {
        $this->path = __DIR__ . '/stubs';
        if (!is_dir($this->path)) {
            mkdir($this->path);
        }
        
        file_put_contents($this->path . '/2026_01_02_000000_create_posts_table.php', "<?php class CreatePostsTable {}");
        file_put_contents($this->path . '/2026_01_01_000000_create_users_table.php', "<?php class CreateUsersTable {}");
    }
    
    protected function tearDown(): void
    {
        unlink($this->path . '/2026_01_02_000000_create_posts_table.php');
        unlink($this->path . '/2026_01_01_000000_create_users_table.php');
        rmdir($this->path);
    }

    public function test_can_load_and_sort_migration_files()
    {
        $loader = new MigrationLoader($this->path);
        
        $files = $loader->getMigrationFiles();
        
        $this->assertCount(2, $files);
        
        // Assert sorted correctly (by date)
        $keys = array_keys($files);
        $this->assertEquals('2026_01_01_000000_create_users_table', $keys[0]);
        $this->assertEquals('2026_01_02_000000_create_posts_table', $keys[1]);
    }
}
