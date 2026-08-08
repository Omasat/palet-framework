<?php

declare(strict_types=1);

namespace Tests\Foundation\Kernel;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Foundation\Kernel\Kernel;
use Palet\Framework\Foundation\Kernel\KernelState;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;

class KernelTest extends TestCase
{
    public function test_kernel_initializes_with_correct_state()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $kernel = new Kernel($app);
        
        $this->assertEquals(KernelState::Initializing->value, $kernel->getState());
    }

    public function test_kernel_bootstraps_and_transitions_state()
    {
        if (version_compare(PHP_VERSION, '8.2.0', '<')) {
            $this->markTestSkipped('Test requires PHP 8.2+ to not throw Exception.');
        }

        $app = $this->createMock(ApplicationInterface::class);
        $kernel = new Kernel($app);
        
        $kernel->bootstrap();
        
        $this->assertEquals(KernelState::Ready->value, $kernel->getState());
    }

    public function test_kernel_terminates_and_transitions_state()
    {
        $app = $this->createMock(ApplicationInterface::class);
        $kernel = new Kernel($app);
        
        $kernel->terminate();
        
        $this->assertEquals(KernelState::Terminated->value, $kernel->getState());
    }
}
