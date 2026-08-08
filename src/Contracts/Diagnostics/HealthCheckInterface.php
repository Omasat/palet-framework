<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Diagnostics;

interface HealthCheckInterface
{
    public function getName(): string;
    public function getDescription(): string;
    
    /**
     * Run the check.
     * Returns true if check passes, false otherwise.
     */
    public function check(): bool;
    
    /**
     * Get error message if check failed.
     */
    public function getErrorMessage(): ?string;
}
