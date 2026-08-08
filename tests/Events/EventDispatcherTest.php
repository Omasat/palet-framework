<?php

declare(strict_types=1);

namespace Tests\Events;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Events\EventDispatcher;

class DummyEvent {}

class EventDispatcherTest extends TestCase
{
    public function test_dispatches_event_to_listener()
    {
        $dispatcher = new EventDispatcher();
        
        $handled = false;
        $dispatcher->listen(DummyEvent::class, function(DummyEvent $event) use (&$handled) {
            $handled = true;
        });

        $this->assertTrue($dispatcher->hasListeners(DummyEvent::class));
        
        $dispatcher->dispatch(new DummyEvent());

        $this->assertTrue($handled);
    }

    public function test_dispatches_until_first_non_null()
    {
        $dispatcher = new EventDispatcher();
        
        $dispatcher->listen('test.event', function() {
            return null;
        });

        $dispatcher->listen('test.event', function() {
            return 'success';
        });

        $dispatcher->listen('test.event', function() {
            return 'should_not_reach_here';
        });

        $result = $dispatcher->dispatchUntil('test.event');

        $this->assertEquals('success', $result);
    }
}
