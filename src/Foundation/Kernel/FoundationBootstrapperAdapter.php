<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Kernel;

use Closure;
use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Foundation\Kernel\BootableInterface;
use Palet\Framework\Foundation\Bootstrap\BootstrapSequence as FoundationBootstrapSequence;

class FoundationBootstrapperAdapter implements BootableInterface
{
    public function bootstrap(ApplicationInterface $app, Closure $next): mixed
    {
        foreach (FoundationBootstrapSequence::get() as $bootstrapper) {
            $instance = method_exists($app, 'make') ? $app->make($bootstrapper) : null;

            if (!is_object($instance)) {
                $instance = new $bootstrapper();
            }

            $instance->bootstrap($app);
        }

        return $next($app);
    }
}
