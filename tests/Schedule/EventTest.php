<?php

declare(strict_types=1);

namespace Tests\Schedule;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Schedule\Event;
use Palet\Framework\Schedule\CronExpression;
use Palet\Framework\Contracts\Schedule\MutexInterface;

class EventTest extends TestCase
{
    protected MutexInterface $mutex;
    protected CronExpression $cron;

    protected function setUp(): void
    {
        $this->mutex = $this->createMock(MutexInterface::class);
        $this->cron = new CronExpression();
    }

    public function test_frequencies_set_correct_cron_expression()
    {
        $event = new Event($this->mutex, $this->cron, 'php art');
        
        $event->everyMinute();
        $this->assertEquals('* * * * *', $event->expression);
        
        $event->hourly();
        $this->assertEquals('0 * * * *', $event->expression);
        
        $event->daily();
        $this->assertEquals('0 0 * * *', $event->expression);
    }

    public function test_when_and_skip_filters()
    {
        $event = new Event($this->mutex, $this->cron, 'php art');
        $event->everyMinute(); // will match any time
        
        $time = new \DateTimeImmutable('2026-08-01 12:30:00');
        
        $this->assertTrue($event->isDue($time));
        
        $event->when(function() {
            return false;
        });
        
        $this->assertFalse($event->isDue($time));
        
        // Reset and test skip
        $event2 = new Event($this->mutex, $this->cron, 'php art');
        $event2->everyMinute();
        
        $event2->skip(function() {
            return true;
        });
        
        $this->assertFalse($event2->isDue($time));
    }
}
