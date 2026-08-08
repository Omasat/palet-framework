<?php

declare(strict_types=1);

namespace Tests\Console\Fixtures;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;

#[AsCommand('fixture:test', 'A test fixture command')]
class FixtureCommand extends Command
{
    protected function execute(): int
    {
        return 0;
    }
}
