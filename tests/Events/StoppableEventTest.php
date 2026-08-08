<?php

declare(strict_types=1);

namespace Tests\Events;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Events\EventDispatcher;
use Palet\Framework\Events\StoppableEventTrait;
use Psr\EventDispatcher\StoppableEventInterface;

class HaltingEvent implements StoppableEventInterface
{
    use StoppableEventTrait;
}

class StoppableEventTest extends TestCase
{
    public function test_stops_propagation()
    {
        $dispatcher = new EventDispatcher();
        
        $order = [];
        
        $dispatcher->listen(HaltingEvent::class, function(HaltingEvent $e) use (&$order) {
            $order[] = 'first';
            $e->stopPropagation();
        }, 100);

        $dispatcher->listen(HaltingEvent::class, function(HaltingEvent $e) use (&$order) {
            $order[] = 'second';
        }, 50);

        $event = new HaltingEvent();
        $dispatcher->dispatch($event);

        $this->assertEquals(['first'], $order);
        $this->assertTrue($event->isPropagationStopped());
    }
}
