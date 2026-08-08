<?php

declare(strict_types=1);

namespace Tests\Database\Orm\Advanced;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Database\Orm\Observer\ObserverManager;
use stdClass;

class MockObserver
{
    public bool $creatingFired = false;
    public bool $deletedFired = false;
    
    public function creating(object $model)
    {
        $this->creatingFired = true;
    }
    
    public function deleted(object $model)
    {
        $this->deletedFired = true;
    }
}

class ObserverTest extends TestCase
{
    public function test_manager_dispatches_events()
    {
        $manager = new ObserverManager();
        $observer = new MockObserver();
        
        $manager->register('UserModel', $observer);
        
        $manager->dispatch('UserModel', 'creating', new stdClass());
        
        $this->assertTrue($observer->creatingFired);
        $this->assertFalse($observer->deletedFired);
        
        $manager->dispatch('UserModel', 'deleted', new stdClass());
        $this->assertTrue($observer->deletedFired);
    }
}
