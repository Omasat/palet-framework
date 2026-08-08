<?php

declare(strict_types=1);

namespace Tests\Events;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Events\EventDispatcher;

class PriorityEvent {}

class ListenerPriorityTest extends TestCase
{
    public function test_listeners_are_called_by_priority()
    {
        $dispatcher = new EventDispatcher();
        
        $order = [];
        
        $dispatcher->listen(PriorityEvent::class, function() use (&$order) {
            $order[] = 'low';
        }, -10);

        $dispatcher->listen(PriorityEvent::class, function() use (&$order) {
            $order[] = 'high';
        }, 100);

        $dispatcher->listen(PriorityEvent::class, function() use (&$order) {
            $order[] = 'normal';
        }, 0);

        $dispatcher->dispatch(new PriorityEvent());

        $this->assertEquals(['high', 'normal', 'low'], $order);
    }
}
