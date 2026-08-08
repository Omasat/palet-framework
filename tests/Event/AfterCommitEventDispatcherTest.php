<?php

declare(strict_types=1);

namespace Tests\Event;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Event\EventDispatcher;
use Palet\Framework\Event\AfterCommitEventDispatcher;

class AfterCommitEventDispatcherTest extends TestCase
{
    public function test_dispatches_normally_when_no_transaction()
    {
        $inner = new EventDispatcher();
        $dispatcher = new AfterCommitEventDispatcher($inner);
        
        $fired = false;
        $inner->listen('test', function() use (&$fired) { $fired = true; });
        
        $dispatcher->dispatch('test');
        
        $this->assertTrue($fired);
    }
    
    public function test_defers_during_transaction_and_fires_on_commit()
    {
        $inner = new EventDispatcher();
        $dispatcher = new AfterCommitEventDispatcher($inner);
        
        $fired = false;
        $inner->listen('test', function() use (&$fired) { $fired = true; });
        
        $dispatcher->beginTransaction();
        $dispatcher->dispatch('test');
        
        $this->assertFalse($fired, 'Event should be deferred');
        
        $dispatcher->commit();
        
        $this->assertTrue($fired, 'Event should fire after commit');
    }
    
    public function test_discards_events_on_rollback()
    {
        $inner = new EventDispatcher();
        $dispatcher = new AfterCommitEventDispatcher($inner);
        
        $fired = false;
        $inner->listen('test', function() use (&$fired) { $fired = true; });
        
        $dispatcher->beginTransaction();
        $dispatcher->dispatch('test');
        
        $dispatcher->rollback();
        
        $this->assertFalse($fired, 'Event should be discarded on rollback');
        
        // Next commit shouldn't fire it either
        $dispatcher->commit();
        $this->assertFalse($fired);
    }
}
