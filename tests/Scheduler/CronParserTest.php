<?php

declare(strict_types=1);

namespace Tests\Scheduler;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Scheduler\CronExpressionParser;
use DateTimeImmutable;

class CronParserTest extends TestCase
{
    public function test_every_minute()
    {
        $parser = new CronExpressionParser();
        $time = new DateTimeImmutable('2026-01-01 12:05:00');
        
        $this->assertTrue($parser->isDue('* * * * *', $time));
    }

    public function test_interval_parsing()
    {
        $parser = new CronExpressionParser();
        
        $timeMatch = new DateTimeImmutable('2026-01-01 12:10:00'); // Multiple of 5
        $this->assertTrue($parser->isDue('*/5 * * * *', $timeMatch));
        
        $timeNoMatch = new DateTimeImmutable('2026-01-01 12:12:00'); // Not multiple of 5
        $this->assertFalse($parser->isDue('*/5 * * * *', $timeNoMatch));
    }
}
