<?php

declare(strict_types=1);

namespace Palet\Framework\Diagnostics;

use Palet\Framework\Contracts\Diagnostics\ReportGeneratorInterface;

class ReportGenerator implements ReportGeneratorInterface
{
    public function generate(array $results, string $format = 'console'): string
    {
        if ($format === 'json') {
            return json_encode($results, JSON_PRETTY_PRINT);
        }

        if ($format === 'markdown') {
            return $this->generateMarkdown($results);
        }

        return $this->generateConsole($results);
    }

    protected function generateConsole(array $results): string
    {
        $output = "Framework Diagnostics Report\n";
        $output .= str_repeat('=', 30) . "\n\n";

        foreach ($results as $result) {
            $status = $result['passed'] ? "\033[32m[PASS]\033[0m" : "\033[31m[FAIL]\033[0m";
            $output .= "{$status} {$result['name']} - {$result['description']}\n";
            if (!$result['passed'] && $result['error']) {
                $output .= "       Error: {$result['error']}\n";
            }
        }

        return $output;
    }
    
    protected function generateMarkdown(array $results): string
    {
        $output = "# Framework Diagnostics Report\n\n";
        $output .= "| Status | Check | Description | Error |\n";
        $output .= "|---|---|---|---|\n";

        foreach ($results as $result) {
            $status = $result['passed'] ? "✅" : "❌";
            $error = $result['error'] ?? '-';
            $output .= "| {$status} | {$result['name']} | {$result['description']} | {$error} |\n";
        }

        return $output;
    }
}
