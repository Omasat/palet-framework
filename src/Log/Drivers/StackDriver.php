<?php

declare(strict_types=1);

namespace Palet\Framework\Log\Drivers;

use Palet\Framework\Contracts\Log\LogDriverInterface;
use Stringable;

class StackDriver implements LogDriverInterface
{
    /**
     * @var array<int, LogDriverInterface>
     */
    protected array $drivers;

    public function __construct(array $drivers)
    {
        $this->drivers = $drivers;
    }

    public function write(string $level, string|Stringable $message, array $context = []): void
    {
        foreach ($this->drivers as $driver) {
            $driver->write($level, $message, $context);
        }
    }
}
