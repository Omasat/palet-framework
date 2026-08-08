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
use Palet\Framework\Diagnostics\ProjectAnalyzer;

#[AsCommand('doctor', 'Perform a full system and project diagnostic')]
class DoctorCommand extends Command
{
    protected function execute(): int
    {
        $this->info("Running Palet Framework Diagnostics...\n");
        
        $engine = new HealthCheckEngine();
        $engine->register(new PhpVersionCheck());
        $engine->register(new EnvironmentCheck(getcwd() . '/.env'));
        $engine->register(new FilePermissionsCheck(getcwd()));
        
        $results = $engine->runAll();
        
        $analyzer = new ProjectAnalyzer();
        $results[] = $analyzer->analyze(getcwd());
        
        $format = $this->option('format') ?? 'console';
        
        $generator = new ReportGenerator();
        $report = $generator->generate($results, $format);
        
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
