<?php

declare(strict_types=1);

namespace Tests\Generator\Module;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Generator\Module\ModuleManifestGenerator;
use Palet\Framework\Generator\CodeGenerator;
use Palet\Framework\Generator\TemplateEngine;
use Palet\Framework\Generator\PlaceholderResolver;
use Palet\Framework\Generator\FileGenerator;

class ModuleManifestGeneratorTest extends TestCase
{
    protected string $modulePath;

    protected function setUp(): void
    {
        $this->modulePath = sys_get_temp_dir() . '/palet_module_test_' . uniqid();
        mkdir($this->modulePath);
        mkdir($this->modulePath . '/Providers'); // Required by generator to put ServiceProvider
    }

    protected function tearDown(): void
    {
        $files = [
            $this->modulePath . '/module.json',
            $this->modulePath . '/Providers/InvoiceServiceProvider.php',
            $this->modulePath . '/Providers'
        ];
        
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
            if (is_dir($file)) rmdir($file);
        }
        rmdir($this->modulePath);
    }

    public function test_generates_manifest_and_provider()
    {
        $codeGen = new CodeGenerator(
            new TemplateEngine(new PlaceholderResolver()),
            new FileGenerator()
        );
        $generator = new ModuleManifestGenerator($codeGen);
        
        $generator->generate('Invoice', $this->modulePath);
        
        $manifestPath = $this->modulePath . '/module.json';
        $providerPath = $this->modulePath . '/Providers/InvoiceServiceProvider.php';
        
        $this->assertFileExists($manifestPath);
        $this->assertFileExists($providerPath);
        
        $manifestData = json_decode(file_get_contents($manifestPath), true);
        $this->assertEquals('Invoice', $manifestData['name']);
        
        $providerContent = file_get_contents($providerPath);
        $this->assertStringContainsString('class InvoiceServiceProvider', $providerContent);
    }
}
