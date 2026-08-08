<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Foundation\Kernel;

interface TerminableInterface
{
    /**
     * Terminate the given component.
     */
    public function terminate(): void;
}
