<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Middleware;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Contracts\Http\Middleware\TerminableMiddlewareInterface;

class MiddlewareTerminator
{
    protected ApplicationInterface $app;

    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    public function terminate(array $middlewareClasses, RequestInterface $request, ResponseInterface $response): void
    {
        foreach ($middlewareClasses as $class) {
            $instance = $this->app->make($class);
            
            if ($instance instanceof TerminableMiddlewareInterface) {
                $instance->terminate($request, $response);
            }
        }
    }
}
