<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Foundation;

/**
 * Amaç: Framework'ün başlatılma (bootstrap) sürecindeki adımları tanımlar.
 * Sorumluluk: Environment yükleme, config yükleme, error handler kaydetme gibi ayağa kalkma işlemlerini (bootstrapper) standartlaştırır.
 * Kullanım Alanı: HTTP ve Console Kernel'lerinin bootstrap (ilklendirme) aşamasında kullanılır.
 * Bağımlılıklar: ApplicationInterface
 * Genişletilebilirlik: İhtiyaca göre özel bootstrapper sınıfları oluşturularak sisteme dahil edilebilir.
 *
 * Örnek Kullanım:
 * $bootstrapper->bootstrap($app);
 */
interface BootstrapInterface
{
    /**
     * Bootstrap the given application.
     */
    public function bootstrap(ApplicationInterface $app): void;
}
