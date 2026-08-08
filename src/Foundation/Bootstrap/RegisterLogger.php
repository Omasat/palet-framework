<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Bootstrap;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;

class RegisterLogger implements BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void
    {
        $app->singleton(
            \Palet\Framework\Contracts\Log\LoggerInterface::class,
            \Palet\Framework\Log\LogManager::class
        );
    }
}
