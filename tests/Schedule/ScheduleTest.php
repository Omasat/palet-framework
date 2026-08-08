<?php

declare(strict_types=1);

namespace Tests\Schedule;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Schedule\Schedule;
use Palet\Framework\Schedule\CronExpression;
use Palet\Framework\Contracts\Schedule\MutexInterface;

class ScheduleTest extends TestCase
{
    public function test_adds_events_to_schedule()
    {
        $mutex = $this->createMock(MutexInterface::class);
        $cron = new CronExpression();
        
        $schedule = new Schedule($mutex, $cron);
        
        $schedule->call(function() {})->daily();
        $schedule->command('cache:clear')->hourly();
        $schedule->job('MyJobClass', 'high')->everyMinute();
        
        $this->assertCount(3, $schedule->events());
    }

    public function test_filters_due_events()
    {
        $mutex = $this->createMock(MutexInterface::class);
        $cron = new CronExpression();
        
        $schedule = new Schedule($mutex, $cron);
        
        // Create an event that runs every minute
        $schedule->command('minute_task')->everyMinute();
        
        // Create an event that runs on exact hour and minute (e.g. 14:00)
        // At 14:30, it should be false
        $schedule->command('daily_task')->cron('0 14 * * *');
        
        $time = new \DateTimeImmutable('2026-08-01 14:30:00');
        
        $due = $schedule->dueEvents($time);
        
        $this->assertCount(1, $due);
        $this->assertEquals('minute_task', array_values($due)[0]->command);
    }
}
