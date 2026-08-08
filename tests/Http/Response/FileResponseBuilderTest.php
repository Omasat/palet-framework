<?php

declare(strict_types=1);

namespace Tests\Http\Response;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Response\Builders\FileResponseBuilder;
use Palet\Framework\Http\Response\Builders\DownloadResponseBuilder;
use InvalidArgumentException;

class FileResponseBuilderTest extends TestCase
{
    protected string $tempFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempFile = tempnam(sys_get_temp_dir(), 'palet_');
        file_put_contents($this->tempFile, 'file contents');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        parent::tearDown();
    }

    public function test_file_response_builder_detects_mime_type()
    {
        $builder = new FileResponseBuilder($this->tempFile);
        $response = $builder->build();
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('file contents', $response->getBody()->getContents());
        $this->assertStringContainsString('text/plain', $response->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('inline; filename="', $response->getHeaderLine('Content-Disposition'));
    }

    public function test_download_response_builder_sets_attachment_disposition()
    {
        $builder = new DownloadResponseBuilder($this->tempFile, 'custom.txt');
        $response = $builder->build();
        
        $this->assertEquals('attachment; filename="custom.txt"', $response->getHeaderLine('Content-Disposition'));
    }

    public function test_throws_exception_for_non_existent_file()
    {
        $this->expectException(InvalidArgumentException::class);
        new FileResponseBuilder('/path/to/invalid/file.txt');
    }
}
