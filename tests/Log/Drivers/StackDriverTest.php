<?php

declare(strict_types=1);

namespace Tests\Log\Drivers;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Log\Drivers\StackDriver;
use Palet\Framework\Contracts\Log\LogDriverInterface;
use Stringable;

class StackDriverTest extends TestCase
{
    public function test_writes_to_all_drivers()
    {
        $driver1 = new class implements LogDriverInterface {
            public bool $written = false;
            public function write(string $level, string|Stringable $message, array $context = []): void {
                $this->written = true;
            }
        };

        $driver2 = clone $driver1;

        $stack = new StackDriver([$driver1, $driver2]);
        $stack->write('info', 'msg');

        $this->assertTrue($driver1->written);
        $this->assertTrue($driver2->written);
    }
}
