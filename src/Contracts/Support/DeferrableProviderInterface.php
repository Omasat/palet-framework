<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Support;

/**
 * Amaç: Sadece ihtiyaç anında yüklenecek olan gecikmeli (deferred) sağlayıcıları tanımlar.
 * Sorumluluk: Sağlayıcının Framework'e hangi arayüzleri/servisleri sağladığını (provides) bildirmesini zorunlu kılar.
 * Bağımlılıklar: ServiceProviderInterface
 */
interface DeferrableProviderInterface
{
    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array;
}
