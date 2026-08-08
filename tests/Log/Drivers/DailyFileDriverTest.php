<?php

declare(strict_types=1);

namespace Tests\Log\Drivers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Log\Drivers\DailyFileDriver;
use Palet\Framework\Log\Formatters\LineFormatter;

class DailyFileDriverTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = __DIR__ . '/_daily_temp';
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        $files = glob($this->dir . '/*');
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($this->dir);
    }

    public function test_writes_to_daily_file()
    {
        $path = $this->dir . '/app.log';
        $driver = new DailyFileDriver($path, 7, new class extends LineFormatter {
            public function format(\Palet\Framework\Log\LogRecord $record): string {
                return $record->message;
            }
        });

        $driver->write('info', 'daily message');

        $expectedPath = $this->dir . '/app-' . date('Y-m-d') . '.log';

        $this->assertTrue(file_exists($expectedPath));
        $this->assertEquals('daily message', file_get_contents($expectedPath));
    }
}
