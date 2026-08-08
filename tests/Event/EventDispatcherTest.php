<?php

declare(strict_types=1);

namespace Tests\Event;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Event\EventDispatcher;
use Palet\Framework\Event\GenericEvent;
use Palet\Framework\Event\Priority;
use Palet\Framework\Contracts\Event\ListenerInterface;
use Palet\Framework\Contracts\Event\EventInterface;
use Palet\Framework\Contracts\Event\SubscriberInterface;
use Palet\Framework\Contracts\Event\EventDispatcherInterface;

class MockListener implements ListenerInterface
{
    public bool $handled = false;
    
    public function handle(EventInterface $event): void
    {
        $this->handled = true;
    }
}

class ExecutionOrderTest
{
    public static array $order = [];
}

class MockSubscriber implements SubscriberInterface
{
    public function subscribe(EventDispatcherInterface $events): void
    {
        $events->listen('User.Login', function() {
            ExecutionOrderTest::$order[] = 'subscriber';
        });
    }
}

class EventDispatcherTest extends TestCase
{
    protected function setUp(): void
    {
        ExecutionOrderTest::$order = [];
    }

    public function test_can_dispatch_string_event_to_callable()
    {
        $dispatcher = new EventDispatcher();
        
        $fired = false;
        $dispatcher->listen('User.Created', function(EventInterface $event) use (&$fired) {
            $fired = true;
            $this->assertEquals('User.Created', $event->getName());
        });
        
        $dispatcher->dispatch('User.Created');
        $this->assertTrue($fired);
    }
    
    public function test_priority_execution_order()
    {
        $dispatcher = new EventDispatcher();
        
        $dispatcher->listen('test', function() { ExecutionOrderTest::$order[] = 'normal'; }, Priority::NORMAL);
        $dispatcher->listen('test', function() { ExecutionOrderTest::$order[] = 'highest'; }, Priority::HIGHEST);
        $dispatcher->listen('test', function() { ExecutionOrderTest::$order[] = 'lowest'; }, Priority::LOWEST);
        
        $dispatcher->dispatch('test');
        
        $this->assertEquals(['highest', 'normal', 'lowest'], ExecutionOrderTest::$order);
    }
    
    public function test_wildcard_listener()
    {
        $dispatcher = new EventDispatcher();
        
        $wildcardFired = false;
        $dispatcher->listen('User.*', function() use (&$wildcardFired) {
            $wildcardFired = true;
        });
        
        $dispatcher->dispatch('User.Created');
        
        $this->assertTrue($wildcardFired);
    }
    
    public function test_subscriber()
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->subscribe(new MockSubscriber());
        
        $dispatcher->dispatch('User.Login');
        
        $this->assertEquals(['subscriber'], ExecutionOrderTest::$order);
    }
}
