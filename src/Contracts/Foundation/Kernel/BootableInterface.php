<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Foundation\Kernel;

interface BootableInterface
{
    /**
     * Bootstrap the given component.
     */
    public function bootstrap(\Palet\Framework\Contracts\Foundation\ApplicationInterface $app, \Closure $next): mixed;
}
