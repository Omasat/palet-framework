<?php

declare(strict_types=1);

namespace Tests\Generator\Entity;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Generator\Entity\EntityGenerator;
use Palet\Framework\Generator\Entity\NamingConventionResolver;
use Palet\Framework\Generator\Entity\DomainNamespaceResolver;
use Palet\Framework\Generator\CodeGenerator;
use Palet\Framework\Generator\TemplateEngine;
use Palet\Framework\Generator\PlaceholderResolver;
use Palet\Framework\Generator\FileGenerator;

class EntityGeneratorTest extends TestCase
{
    protected string $destinationDir;

    protected function setUp(): void
    {
        $this->destinationDir = sys_get_temp_dir() . '/palet_domain_' . uniqid();
        mkdir($this->destinationDir);
    }

    protected function tearDown(): void
    {
        // Recursive directory deletion
        $this->deleteDir($this->destinationDir);
    }
    
    protected function deleteDir(string $dirPath) {
        if (!is_dir($dirPath)) {
            return;
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function test_bulk_generates_entity_and_repository()
    {
        $codeGen = new CodeGenerator(
            new TemplateEngine(new PlaceholderResolver()),
            new FileGenerator()
        );
        
        $generator = new EntityGenerator(
            $codeGen,
            new NamingConventionResolver(),
            new DomainNamespaceResolver('App\\Domain'),
            $this->destinationDir
        );
        
        $generator->generateBulk('Product', [
            'components' => ['entity', 'repository', 'repository_interface']
        ]);
        
        $entityPath = $this->destinationDir . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Product' . DIRECTORY_SEPARATOR . 'Entities' . DIRECTORY_SEPARATOR . 'Product.php';
        $repoPath = $this->destinationDir . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Product' . DIRECTORY_SEPARATOR . 'Repositories' . DIRECTORY_SEPARATOR . 'ProductRepository.php';
        $repoIntPath = $this->destinationDir . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Product' . DIRECTORY_SEPARATOR . 'Repositories' . DIRECTORY_SEPARATOR . 'ProductRepositoryInterface.php';
        
        $this->assertFileExists($entityPath);
        $this->assertFileExists($repoPath);
        $this->assertFileExists($repoIntPath);
        
        $entityContent = file_get_contents($entityPath);
        $this->assertStringContainsString('namespace App\Domain\Product\Entities;', $entityContent);
        $this->assertStringContainsString('class Product', $entityContent);
        
        $repoContent = file_get_contents($repoPath);
        $this->assertStringContainsString('implements ProductRepositoryInterface', $repoContent);
    }
}
