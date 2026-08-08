<?php

declare(strict_types=1);

namespace Tests\Log\Formatters;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Log\Formatters\JsonFormatter;
use Palet\Framework\Log\LogRecord;
use DateTimeImmutable;

class JsonFormatterTest extends TestCase
{
    public function test_formats_record_to_json()
    {
        $formatter = new JsonFormatter('Y-m-d');
        
        $date = new DateTimeImmutable('2026-01-01');
        $record = new LogRecord('info', 'System boot', ['user_id' => 1], $date);

        $output = $formatter->format($record);
        $json = json_decode($output, true);

        $this->assertEquals('2026-01-01', $json['datetime']);
        $this->assertEquals('INFO', $json['level']);
        $this->assertEquals('System boot', $json['message']);
        $this->assertEquals(['user_id' => 1], $json['context']);
    }
}
