<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Commands\Diagnostics;

use Palet\Framework\Console\Command;
use Palet\Framework\Console\Attributes\AsCommand;
use Palet\Framework\Diagnostics\HealthCheckEngine;
use Palet\Framework\Diagnostics\Checks\PhpVersionCheck;
use Palet\Framework\Diagnostics\Checks\FilePermissionsCheck;
use Palet\Framework\Diagnostics\Checks\EnvironmentCheck;
use Palet\Framework\Diagnostics\ReportGenerator;

#[AsCommand('project:health', 'Run project health checks')]
class ProjectHealthCommand extends Command
{
    protected function execute(): int
    {
        $engine = new HealthCheckEngine();
        $engine->register(new PhpVersionCheck());
        $engine->register(new EnvironmentCheck(getcwd() . '/.env'));
        $engine->register(new FilePermissionsCheck(getcwd()));
        
        $results = $engine->runAll();
        
        $generator = new ReportGenerator();
        $report = $generator->generate($results, 'console');
        
        $this->line($report);
        
        $hasFailures = false;
        foreach ($results as $result) {
            if (!$result['passed']) {
                $hasFailures = true;
                break;
            }
        }
        
        return $hasFailures ? 1 : 0;
    }
}
