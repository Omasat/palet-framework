<?php

declare(strict_types=1);

namespace Tests\Foundation\Kernel;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Kernel\TerminationManager;
use Palet\Framework\Contracts\Foundation\Kernel\TerminableInterface;
use RuntimeException;

class DummyTerminable implements TerminableInterface
{
    public bool $terminated = false;

    public function terminate(): void
    {
        $this->terminated = true;
    }
}

class FailingTerminable implements TerminableInterface
{
    public function terminate(): void
    {
        throw new RuntimeException("Should be swallowed");
    }
}

class TerminationManagerTest extends TestCase
{
    public function test_terminates_registered_components()
    {
        $manager = new TerminationManager();
        
        $component1 = new DummyTerminable();
        $component2 = new DummyTerminable();
        
        $manager->register($component1);
        $manager->register($component2);
        
        $manager->terminate();
        
        $this->assertTrue($component1->terminated);
        $this->assertTrue($component2->terminated);
    }

    public function test_swallows_exceptions_during_termination()
    {
        $manager = new TerminationManager();
        
        $component1 = new FailingTerminable();
        $component2 = new DummyTerminable();
        
        $manager->register($component1);
        $manager->register($component2);
        
        $manager->terminate();
        
        // Ensure exception was swallowed and component2 still terminated
        $this->assertTrue($component2->terminated);
    }
}
