<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Support;

/**
 * Amaç: Servis sağlayıcılarının (Service Provider) zorunlu kıldığı standartları belirler.
 * Sorumluluk: Framework'e modül, paket veya core servisleri kayıt etmek (register) ve başlatmak (boot).
 * Bağımlılıklar: Yok
 * Genişletilebilirlik: İhtiyaca göre özel metodlar içeren arayüzlerle genişletilebilir.
 */
interface ServiceProviderInterface
{
    /**
     * Register any application services.
     */
    public function register(): void;

    /**
     * Bootstrap any application services.
     */
    public function boot(): void;
}
