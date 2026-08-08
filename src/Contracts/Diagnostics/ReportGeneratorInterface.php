<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Diagnostics;

interface ReportGeneratorInterface
{
    /**
     * Generate report from diagnostics results.
     */
    public function generate(array $results): string;
}
