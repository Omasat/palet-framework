<?php

declare(strict_types=1);

namespace Tests\Database\Migrations;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Migrations\MigrationRunner;
use Palet\Framework\Database\Migrations\MigrationRepository;
use Palet\Framework\Database\Migrations\MigrationLoader;

class MigrationRunnerTest extends TestCase
{
    protected string $path;

    protected function setUp(): void
    {
        $this->path = __DIR__ . '/stubs';
        if (!is_dir($this->path)) {
            mkdir($this->path);
        }
        
        file_put_contents($this->path . '/2026_01_01_000000_create_users_table.php', <<<EOF
<?php
use Palet\Framework\Contracts\Database\Migrations\MigrationInterface;
class CreateUsersTable implements MigrationInterface {
    public static bool \$upRan = false;
    public static bool \$downRan = false;
    public function up(): void { self::\$upRan = true; }
    public function down(): void { self::\$downRan = true; }
}
EOF
        );
    }
    
    protected function tearDown(): void
    {
        unlink($this->path . '/2026_01_01_000000_create_users_table.php');
        rmdir($this->path);
    }

    public function test_runner_executes_pending_migrations()
    {
        $repository = new MigrationRepository();
        $loader = new MigrationLoader($this->path);
        $runner = new MigrationRunner($repository, $loader);
        
        $executed = $runner->run();
        
        $this->assertCount(1, $executed);
        $this->assertEquals('2026_01_01_000000_create_users_table', $executed[0]);
        $this->assertCount(1, $repository->getRan());
        
        $this->assertTrue(\CreateUsersTable::$upRan);
    }

    public function test_runner_rolls_back_last_batch()
    {
        $repository = new MigrationRepository();
        $loader = new MigrationLoader($this->path);
        $runner = new MigrationRunner($repository, $loader);
        
        $runner->run(); // Batch 1
        
        $rolledBack = $runner->rollback();
        
        $this->assertCount(1, $rolledBack);
        $this->assertEmpty($repository->getRan());
        $this->assertTrue(\CreateUsersTable::$downRan);
    }
}
