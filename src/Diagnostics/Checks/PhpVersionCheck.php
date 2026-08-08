<?php

declare(strict_types=1);

namespace Palet\Framework\Diagnostics\Checks;

use Palet\Framework\Contracts\Diagnostics\HealthCheckInterface;

class PhpVersionCheck implements HealthCheckInterface
{
    protected string $requiredVersion = '8.2.0';

    public function getName(): string
    {
        return 'PHP Version';
    }

    public function getDescription(): string
    {
        return 'Checks if PHP version meets framework requirements.';
    }

    public function check(): bool
    {
        return version_compare(PHP_VERSION, $this->requiredVersion, '>=');
    }

    public function getErrorMessage(): ?string
    {
        return 'PHP version ' . $this->requiredVersion . ' or greater is required. Current version: ' . PHP_VERSION;
    }
}
