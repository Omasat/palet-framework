<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Bootstrap;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Foundation\FrameworkState;

class RegisterServiceProviders implements BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void
    {
        if (method_exists($app, 'setState')) {
            $app->setState(FrameworkState::RegisteringProviders);
        }

        if ($app->has('config')) {
            $providers = $app->make('config')->get('app.providers', []);
        } else {
            $providers = [];
        }

        $repository = new \Palet\Framework\Foundation\Providers\ProviderRepository($app);
        $repository->load($providers);
    }
}
