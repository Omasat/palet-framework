<?php

declare(strict_types=1);

namespace Tests\Database\Migrations\Generator;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Migrations\Generator\MigrationGenerator;
use Palet\Framework\Database\Migrations\Generator\MigrationNameResolver;
use Palet\Framework\Generator\CodeGenerator;
use Palet\Framework\Generator\TemplateEngine;
use Palet\Framework\Generator\PlaceholderResolver;
use Palet\Framework\Generator\FileGenerator;

class MigrationGeneratorTest extends TestCase
{
    protected string $destinationDir;

    protected function setUp(): void
    {
        $this->destinationDir = sys_get_temp_dir() . '/palet_migrations_' . uniqid();
        mkdir($this->destinationDir);
    }

    protected function tearDown(): void
    {
        $files = glob($this->destinationDir . '/*');
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($this->destinationDir);
    }

    public function test_generates_create_migration_with_timestamp()
    {
        $codeGen = new CodeGenerator(
            new TemplateEngine(new PlaceholderResolver()),
            new FileGenerator()
        );
        $generator = new MigrationGenerator($codeGen, new MigrationNameResolver());
        
        $path = $generator->generate('create_orders_table', $this->destinationDir);
        
        $this->assertNotNull($path);
        $this->assertFileExists($path);
        
        // Assert filename starts with timestamp like 2026_08_06
        $basename = basename($path);
        $this->assertMatchesRegularExpression('/^\d{4}_\d{2}_\d{2}_\d{6}_create_orders_table\.php$/', $basename);
        
        $content = file_get_contents($path);
        $this->assertStringContainsString("Schema::create('orders',", $content);
    }
    
    public function test_generates_update_migration()
    {
        $codeGen = new CodeGenerator(
            new TemplateEngine(new PlaceholderResolver()),
            new FileGenerator()
        );
        $generator = new MigrationGenerator($codeGen, new MigrationNameResolver());
        
        $path = $generator->generate('add_status_to_orders_table', $this->destinationDir);
        
        $this->assertNotNull($path);
        
        $content = file_get_contents($path);
        $this->assertStringContainsString("Schema::table('orders',", $content);
    }
}
