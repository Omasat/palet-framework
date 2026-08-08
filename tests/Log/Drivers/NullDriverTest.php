<?php

declare(strict_types=1);

namespace Tests\Log\Drivers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Log\Drivers\NullDriver;

class NullDriverTest extends TestCase
{
    public function test_does_nothing()
    {
        $driver = new NullDriver();
        $driver->write('info', 'msg');
        $this->assertTrue(true); // Should not throw
    }
}
