<?php

declare(strict_types=1);

namespace Tests\Support;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Support\Date;

class DateTest extends TestCase
{
    public function test_now_and_today()
    {
        $now = Date::now();
        $this->assertInstanceOf(Date::class, $now);

        $today = Date::today();
        $this->assertEquals('00:00:00', $today->format('H:i:s'));
    }

    public function test_add_and_sub_days()
    {
        $date = Date::now()->setDate(2026, 1, 10);
        
        $added = $date->addDays(5);
        $this->assertEquals('2026-01-15', $added->format('Y-m-d'));
        
        // Assert immutable
        $this->assertEquals('2026-01-10', $date->format('Y-m-d'));

        $subbed = $date->subDays(5);
        $this->assertEquals('2026-01-05', $subbed->format('Y-m-d'));
    }
}
