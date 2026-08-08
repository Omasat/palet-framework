<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\Dispatcher;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Routing\Dispatcher\ControllerResolverInterface;
use RuntimeException;

class ControllerResolver implements ControllerResolverInterface
{
    protected ApplicationInterface $app;

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    public function resolve(string $class): object
    {
        if (!class_exists($class)) {
            throw new RuntimeException("Controller class [{$class}] does not exist.");
        }

        return $this->app->make($class);
    }
}
