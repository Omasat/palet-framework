<?php

declare(strict_types=1);

namespace Tests\Console\Fixtures;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Console\Attributes\HiddenCommand;

#[AsCommand('fixture:hidden', 'A hidden fixture command')]
#[HiddenCommand]
class HiddenFixtureCommand extends Command
{
    protected function execute(): int
    {
        return 0;
    }
}
