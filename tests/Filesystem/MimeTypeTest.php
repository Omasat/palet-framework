<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Filesystem\MimeTypeDetector;

class MimeTypeTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = __DIR__ . '/test.txt';
        file_put_contents($this->tempFile, 'hello world');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function test_detects_mime_type()
    {
        $detector = new MimeTypeDetector();
        $mime = $detector->detect($this->tempFile);
        
        $this->assertStringContainsString('text/plain', $mime);
    }

    public function test_returns_false_if_file_not_exists()
    {
        $detector = new MimeTypeDetector();
        $this->assertFalse($detector->detect(__DIR__ . '/not_exist.txt'));
    }
}
