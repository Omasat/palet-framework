<?php

declare(strict_types=1);

namespace Tests\Foundation\Providers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Providers\ProviderManifest;

class ProviderManifestTest extends TestCase
{
    private string $manifestPath;

    protected function setUp(): void
    {
        $this->manifestPath = __DIR__ . '/_temp_manifest.php';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->manifestPath)) {
            unlink($this->manifestPath);
        }
    }

    public function test_writes_and_loads_manifest()
    {
        $manifestObj = new ProviderManifest($this->manifestPath);

        $data = [
            'providers' => ['TestProvider'],
            'deferred' => ['mailer' => 'LazyMailerProvider']
        ];

        $manifestObj->write($data);
        
        $this->assertTrue($manifestObj->exists());
        
        $loaded = $manifestObj->load();
        
        $this->assertEquals($data, $loaded);
    }
}
