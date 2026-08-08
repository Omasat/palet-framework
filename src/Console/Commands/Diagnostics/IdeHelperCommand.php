<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Diagnostics;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Diagnostics\IDE\IDEHelperGenerator;

#[AsCommand('ide:helper', 'Generate IDE helper file for auto-completion')]
class IdeHelperCommand extends Command
{
    protected function execute(): int
    {
        $generator = new IDEHelperGenerator();
        $generator->generate(getcwd());
        
        $this->info("IDE helper file '_ide_helper.php' generated successfully.");
        return 0;
    }
}
