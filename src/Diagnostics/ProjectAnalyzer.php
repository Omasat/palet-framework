<?php

declare(strict_types=1);

namespace Palet\Framework\Diagnostics;

class ProjectAnalyzer
{
    public function analyze(string $basePath): array
    {
        $issues = [];
        
        $composerJsonPath = $basePath . DIRECTORY_SEPARATOR . 'composer.json';
        if (!file_exists($composerJsonPath)) {
            $issues[] = 'composer.json is missing.';
        } else {
            $composerContent = json_decode(file_get_contents($composerJsonPath), true);
            if (!isset($composerContent['autoload']['psr-4'])) {
                $issues[] = 'No PSR-4 autoloading defined in composer.json.';
            }
        }
        
        // In a real implementation this would scan routes, controllers, middleware, etc.
        
        return [
            'name' => 'Project Analysis',
            'description' => 'Static analysis of project structure.',
            'passed' => empty($issues),
            'error' => empty($issues) ? null : implode(' ', $issues)
        ];
    }
}
