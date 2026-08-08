<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Bootstrap;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;

class BootServiceProviders implements BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void
    {
        $app->boot();
    }
}
