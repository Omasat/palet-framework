<?php

declare(strict_types=1);

namespace Tests\Events;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Events\EventDispatcher;

class WildcardTest extends TestCase
{
    public function test_matches_wildcard_events()
    {
        $dispatcher = new EventDispatcher();
        
        $handled = [];
        
        $dispatcher->listen('user.*', function($event) use (&$handled) {
            $handled[] = 'wildcard';
        });

        $dispatcher->listen('user.created', function($event) use (&$handled) {
            $handled[] = 'specific';
        });

        $dispatcher->dispatchUntil('user.created');
        
        $this->assertContains('wildcard', $handled);
        $this->assertContains('specific', $handled);
    }
}
