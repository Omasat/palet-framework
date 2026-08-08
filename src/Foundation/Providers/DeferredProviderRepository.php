<?php

declare(strict_types=1);

namespace Palet\Framework\Foundation\Providers;

use Palet\Framework\Contracts\Foundation\ApplicationInterface;

class DeferredProviderRepository
{
    /**
     * Application instance.
     */
    protected ApplicationInterface $app;

    /**
     * @var array<string, string>
     */
    protected array $deferredServices = [];

    /**
     * @param ApplicationInterface $app
     */
    public function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Load the deferred provider for the given service.
     */
    public function load(string $service): void
    {
        if (isset($this->deferredServices[$service])) {
            $provider = $this->deferredServices[$service];
            
            $this->app->register($provider);
            
            // Eğer bir sağlayıcı yüklendiyse, onun sağladığı diğer tüm
            // servisleri de "deferred" listesinden temizleyebiliriz.
            $this->removeProviderFromDeferred($provider);
        }
    }

    /**
     * Gecikmeli servisleri (Deferred Services) listeye atar.
     */
    public function setDeferredServices(array $services): void
    {
        $this->deferredServices = $services;
    }

    /**
     * Zaten yüklenmiş olan sağlayıcının sunduğu tüm servisleri kayıtlı listeden çıkarır.
     */
    protected function removeProviderFromDeferred(string $provider): void
    {
        $this->deferredServices = array_filter(
            $this->deferredServices,
            fn($p) => $p !== $provider
        );
    }
}
