<?php

declare(strict_types=1);

namespace Tests\Schedule;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Schedule\CronExpression;

class CronExpressionTest extends TestCase
{
    public function test_matches_asterisk()
    {
        $cron = new CronExpression();
        $time = new \DateTimeImmutable('2026-08-01 12:30:00');
        
        $this->assertTrue($cron->isDue($time, '* * * * *'));
    }

    public function test_matches_exact_minute()
    {
        $cron = new CronExpression();
        $time = new \DateTimeImmutable('2026-08-01 12:30:00');
        
        // 30th minute
        $this->assertTrue($cron->isDue($time, '30 * * * *'));
        $this->assertFalse($cron->isDue($time, '31 * * * *'));
    }

    public function test_matches_step_values()
    {
        $cron = new CronExpression();
        $time1 = new \DateTimeImmutable('2026-08-01 12:30:00');
        $time2 = new \DateTimeImmutable('2026-08-01 12:31:00');
        
        // Every 5 minutes
        $this->assertTrue($cron->isDue($time1, '*/5 * * * *'));
        $this->assertFalse($cron->isDue($time2, '*/5 * * * *'));
    }

    public function test_returns_false_on_invalid_format()
    {
        $cron = new CronExpression();
        $time = new \DateTimeImmutable('2026-08-01 12:30:00');
        
        $this->assertFalse($cron->isDue($time, 'invalid cron'));
    }
}
