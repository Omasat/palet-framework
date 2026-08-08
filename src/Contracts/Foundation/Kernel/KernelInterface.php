<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Foundation\Kernel;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;

interface KernelInterface
{
    /**
     * Get the current state of the kernel.
     */
    public function getState(): string;

    /**
     * Bootstrap the application.
     */
    public function bootstrap(): void;

    /**
     * Terminate the application gracefully.
     */
    public function terminate(): void;

    /**
     * Get the application instance.
     */
    public function getApplication(): ApplicationInterface;
}
