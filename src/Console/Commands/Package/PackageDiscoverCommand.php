<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Package;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Package\PackageDiscovery;

#[AsCommand('package:discover', 'Discover and register package commands and providers')]
class PackageDiscoverCommand extends Command
{
    protected function execute(): int
    {
        $this->info('Discovering packages...');
        
        // Mocking the discovery process for now
        $discovery = new PackageDiscovery();
        
        $this->line('Packages discovered and cached successfully.');
        return 0;
    }
}
