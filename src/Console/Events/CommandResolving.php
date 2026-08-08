<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Events;

class CommandResolving
{
    public function __construct(
        public readonly string $commandName
    ) {}
}
