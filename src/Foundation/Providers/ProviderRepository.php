<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Providers;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Support\ServiceProviderInterface;

class ProviderRepository
{
    /**
     * Application instance.
     */
    protected ApplicationInterface $app;

    /**
     * @param ApplicationInterface $app
     */
    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Register an array of service providers.
     *
     * @param array<int, string|ServiceProviderInterface> $providers
     */
    public function load(array $providers): void
    {
        $manifestPath = method_exists($this->app, 'bootstrapPath') 
            ? $this->app->bootstrapPath('cache/providers.php') 
            : '';

        if ($manifestPath !== '' && file_exists($manifestPath)) {
            $manifest = require $manifestPath;
            if (isset($manifest['providers'])) {
                $providers = $manifest['providers'];
            }
        }

        foreach ($providers as $provider) {
            $this->app->register($provider);
        }
    }
}
