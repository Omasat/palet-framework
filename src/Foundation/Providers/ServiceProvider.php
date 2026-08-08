<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Providers;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;
use Palet\Framework\Contracts\Support\ServiceProviderInterface;

/**
 * Temel Service Provider sınıfı.
 * Bütün uygulama ve paket sağlayıcıları bu sınıftan kalıtım (extends) almalıdır.
 */
abstract class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Uygulama (Container) örneği.
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
     * Register any application services.
     */
    public function register(): void
    {
        // Temel sınıfta opsiyonel bırakılabilir,
        // ancak alt sınıflar gerektiğinde ezecektir (override).
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Temel sınıfta opsiyonel bırakılabilir.
    }
}
