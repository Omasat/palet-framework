<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Foundation;

use Palet\Framework\Contracts\Container\ContainerInterface;
use Palet\Framework\Contracts\Support\ServiceProviderInterface;

/**
 * Amaç: Uygulamanın en temel bileşeni olup tüm sistemi sarmalar.
 * Sorumluluk: IoC Container'ı yönetmek, servis sağlayıcıları başlatmak ve temel uygulama yollarını bilmek.
 * Kullanım Alanı: Uygulamanın bootstrap (ayağa kalkma) aşamasında ve genel Container çözümlerinde.
 * Bağımlılıklar: ContainerInterface
 * Genişletilebilirlik: Yeni uygulama yolları, çevre (environment) algılama yöntemleri eklenebilir.
 *
 * Örnek Kullanım:
 * $app = new Application(dirname(__DIR__));
 * $app->make(RouterInterface::class);
 */
interface ApplicationInterface extends ContainerInterface
{
    /**
     * Get the version number of the application.
     */
    public function version(): string;

    /**
     * Get the base path of the application installation.
     */
    public function basePath(string $path = ''): string;

    /**
     * Determine if the application is running in the console.
     */
    public function runningInConsole(): bool;

    /**
     * Boot the application's service providers.
     */
    public function boot(): void;

    /**
     * Register a service provider with the application.
     */
    public function register(string|ServiceProviderInterface $provider, bool $force = false): ServiceProviderInterface;

    /**
     * Register all of the configured providers.
     */
    public function registerConfiguredProviders(): void;
}
