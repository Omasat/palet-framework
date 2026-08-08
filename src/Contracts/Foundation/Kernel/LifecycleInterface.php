<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Foundation\Kernel;

interface LifecycleInterface
{
    /**
     * Handle the given state transition.
     */
    public function onStateChange(string $from, string $to): void;
}
