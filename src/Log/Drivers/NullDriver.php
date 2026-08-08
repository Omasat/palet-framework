<?php

declare(strict_types=1);

namespace Palet\Framework\Log\Drivers;

use Palet\Framework\Contracts\Log\LogDriverInterface;
use Stringable;

class NullDriver implements LogDriverInterface
{
    public function write(string $level, string|Stringable $message, array $context = []): void
    {
        // Do nothing.
    }
}
