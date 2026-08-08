<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Asset;

interface DevServerInterface
{
    /**
     * Determine if the development server is running.
     */
    public function isRunning(): bool;

    /**
     * Get the URL of the development server.
     */
    public function url(): ?string;
}
