<?php

declare(strict_types=1);

namespace Tests\Console\Fixtures;

use Palet\Framework\Console\Command;

class LegacyCommand extends Command
{
    protected string $name = 'fixture:legacy';
    protected string $description = 'A legacy command without attributes';

    protected function execute(): int
    {
        return 0;
    }
}
