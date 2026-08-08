<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Kernel;

use Palet\Framework\Contracts\Foundation\Kernel\TerminableInterface;
use Throwable;

class TerminationManager
{
    /**
     * @var array<int, TerminableInterface>
     */
    protected array $terminables = [];

    public function register(TerminableInterface $terminable): void
    {
        $this->terminables[] = $terminable;
    }

    public function terminate(): void
    {
        foreach ($this->terminables as $terminable) {
            try {
                $terminable->terminate();
            } catch (Throwable $e) {
                // Silently ignore termination errors to allow other components to terminate
            }
        }
    }
}
