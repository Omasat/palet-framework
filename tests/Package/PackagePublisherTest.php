<?php

declare(strict_types=1);

namespace Tests\Package;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Package\PackagePublisher;

class PackagePublisherTest extends TestCase
{
    protected string $projectRoot;
    protected string $packageRoot;

    protected function setUp(): void
    {
        $this->projectRoot = sys_get_temp_dir() . '/palet_project_' . uniqid();
        $this->packageRoot = sys_get_temp_dir() . '/palet_package_' . uniqid();
        
        mkdir($this->projectRoot);
        mkdir($this->packageRoot);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->projectRoot);
        $this->removeDirectory($this->packageRoot);
    }

    protected function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    public function test_publishes_files()
    {
        // Setup package manifest and files
        $manifest = [
            'name' => 'test/package',
            'extra' => [
                'palet' => [
                    'publish' => [
                        'config' => [
                            'config/test.php' => 'config/test.php'
                        ]
                    ]
                ]
            ]
        ];
        
        file_put_contents($this->packageRoot . '/palet.json', json_encode($manifest));
        
        mkdir($this->packageRoot . '/config');
        file_put_contents($this->packageRoot . '/config/test.php', '<?php return [];');
        
        // Publish
        $publisher = new PackagePublisher($this->projectRoot);
        $publisher->publish($this->packageRoot, ['config']);
        
        $this->assertFileExists($this->projectRoot . '/config/test.php');
    }
}
