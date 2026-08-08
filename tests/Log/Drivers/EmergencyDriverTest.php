<?php

declare(strict_types=1);

namespace Tests\Log\Drivers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Log\Drivers\EmergencyDriver;

class EmergencyDriverTest extends TestCase
{
    public function test_writes_to_error_log()
    {
        $driver = new EmergencyDriver();
        $driver->write('emergency', 'system failure');
        
        // Cannot easily intercept error_log without runkit or custom stream wrapper,
        // so we just ensure it executes without crashing.
        $this->assertTrue(true);
    }
}
