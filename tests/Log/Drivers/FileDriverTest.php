<?php

declare(strict_types=1);

namespace Tests\Log\Drivers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Log\Drivers\FileDriver;
use Palet\Framework\Log\Formatters\LineFormatter;

class FileDriverTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        $this->path = __DIR__ . '/_temp.log';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->path)) {
            unlink($this->path);
        }
    }

    public function test_writes_to_file()
    {
        $formatter = new class extends LineFormatter {
            public function format(\Palet\Framework\Log\LogRecord $record): string {
                return $record->level . ':' . $record->message;
            }
        };

        $driver = new FileDriver($this->path, $formatter);
        $driver->write('info', 'test message');

        $this->assertTrue(file_exists($this->path));
        $this->assertEquals('info:test message', file_get_contents($this->path));
    }
}
