<?php

declare(strict_types=1);

namespace Tests\Log\Formatters;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Log\Formatters\LineFormatter;
use Palet\Framework\Log\LogRecord;
use DateTimeImmutable;

class LineFormatterTest extends TestCase
{
    public function test_formats_record_to_line()
    {
        $formatter = new LineFormatter("[%datetime%] %level_name%: %message% %context%\n", 'Y-m-d');
        
        $date = new DateTimeImmutable('2026-01-01');
        $record = new LogRecord('info', 'System boot', ['user_id' => 1], $date);

        $output = $formatter->format($record);

        $this->assertEquals("[2026-01-01] INFO: System boot {\"user_id\":1}\n", $output);
    }

    public function test_formats_without_context()
    {
        $formatter = new LineFormatter("[%datetime%] %level_name%: %message% %context%\n", 'Y-m-d');
        
        $date = new DateTimeImmutable('2026-01-01');
        $record = new LogRecord('error', 'Disk full', [], $date);

        $output = $formatter->format($record);

        $this->assertEquals("[2026-01-01] ERROR: Disk full \n", $output);
    }
}
