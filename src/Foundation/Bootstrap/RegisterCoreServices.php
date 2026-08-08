<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Bootstrap;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;

class RegisterCoreServices implements BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void
    {
        $app->singleton(
            \Palet\Framework\Contracts\Routing\RouterInterface::class,
            function ($app) {
                return new \Palet\Framework\Routing\Router(null, $app);
            }
        );

        $app->singleton(
            \Palet\Framework\Contracts\Routing\Dispatcher\ControllerDispatcherInterface::class,
            \Palet\Framework\Routing\Dispatcher\ControllerDispatcher::class
        );
        
        $app->singleton(
            \Palet\Framework\Contracts\Support\Invocation\MethodInvokerInterface::class,
            \Palet\Framework\Support\Invocation\MethodInvoker::class
        );
        
        $app->singleton(
            \Palet\Framework\Contracts\Support\Invocation\ParameterResolverInterface::class,
            \Palet\Framework\Support\Invocation\ParameterResolver::class
        );
        
        $app->singleton(
            \Palet\Framework\Support\Invocation\DependencyResolver::class,
            \Palet\Framework\Support\Invocation\DependencyResolver::class
        );
        
        $app->singleton(
            \Palet\Framework\Support\Invocation\ArgumentMapper::class,
            \Palet\Framework\Support\Invocation\ArgumentMapper::class
        );
        
        $app->singleton(
            \Palet\Framework\Support\Invocation\ReflectionMetadataCache::class,
            \Palet\Framework\Support\Invocation\ReflectionMetadataCache::class
        );
        
        $app->singleton(
            \Palet\Framework\Routing\Dispatcher\ActionResolver::class,
            \Palet\Framework\Routing\Dispatcher\ActionResolver::class
        );
        
        $app->singleton(
            \Palet\Framework\Routing\Dispatcher\ControllerResolver::class,
            \Palet\Framework\Routing\Dispatcher\ControllerResolver::class
        );
        
        $app->singleton(
            \Palet\Framework\Routing\Dispatcher\ActionResultNormalizer::class,
            \Palet\Framework\Routing\Dispatcher\ActionResultNormalizer::class
        );
    }
}
