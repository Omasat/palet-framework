<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Diagnostics;

interface IDEIntegrationInterface
{
    /**
     * Generate IDE meta files for auto-completion.
     */
    public function generate(string $basePath): void;
}
