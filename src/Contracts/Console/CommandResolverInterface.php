<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Console;

interface CommandResolverInterface
{
    /**
     * Resolve a command instance by its name.
     */
    public function resolve(string $commandName): ?CommandInterface;
}
