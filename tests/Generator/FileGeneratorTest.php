<?php

declare(strict_types=1);

namespace Tests\Generator;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Generator\FileGenerator;
use RuntimeException;

class FileGeneratorTest extends TestCase
{
    protected string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = sys_get_temp_dir() . '/palet_generator_test_' . uniqid() . '.php';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function test_generates_file()
    {
        $generator = new FileGenerator();
        
        $result = $generator->generate($this->tempFile, '<?php echo "test";', false, false);
        
        $this->assertTrue($result);
        $this->assertFileExists($this->tempFile);
        $this->assertEquals('<?php echo "test";', file_get_contents($this->tempFile));
    }

    public function test_prevents_overwrite_without_force()
    {
        file_put_contents($this->tempFile, 'original');
        
        $generator = new FileGenerator();
        
        $this->expectException(RuntimeException::class);
        $generator->generate($this->tempFile, 'new', false, false);
    }

    public function test_allows_overwrite_with_force()
    {
        file_put_contents($this->tempFile, 'original');
        
        $generator = new FileGenerator();
        $generator->generate($this->tempFile, 'new', true, false);
        
        $this->assertEquals('new', file_get_contents($this->tempFile));
    }

    public function test_dry_run_does_not_create_file()
    {
        $generator = new FileGenerator();
        
        $result = $generator->generate($this->tempFile, 'test', false, true);
        
        $this->assertTrue($result); // Dry run returns true indicating success simulation
        $this->assertFileDoesNotExist($this->tempFile);
    }
}
