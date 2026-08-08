<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Bootstrap;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;

/**
 * Bootstrap (Ayağa Kalkma) sürecindeki her bir adımı temsil eder.
 */
interface BootstrapperInterface
{
    /**
     * Bootstrap the given application.
     */
    public function bootstrap(ApplicationInterface $app): void;
}
