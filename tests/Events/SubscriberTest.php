<?php

declare(strict_types=1);

namespace Tests\Events;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Events\EventDispatcher;
use Palet\Framework\Contracts\Events\SubscriberInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;

class DummySubscriber implements SubscriberInterface
{
    public bool $handled = false;

    public function subscribe(EventDispatcherInterface $events): void
    {
        $events->listen('sub.event', [$this, 'handleEvent']);
    }

    public function handleEvent($event)
    {
        $this->handled = true;
    }
}

class SubscriberTest extends TestCase
{
    public function test_registers_subscriber()
    {
        $dispatcher = new EventDispatcher();
        $subscriber = new DummySubscriber();
        
        $dispatcher->subscribe($subscriber);
        
        $this->assertTrue($dispatcher->hasListeners('sub.event'));
        
        $dispatcher->dispatchUntil('sub.event');
        
        $this->assertTrue($subscriber->handled);
    }
}
