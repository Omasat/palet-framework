<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Logging;

use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * Amaç: Sistem hatalarını, bilgileri ve izleri loglamak için standart bir yapı sunar.
 * Sorumluluk: PSR-3 loglama standartlarına uyarak uygulama loglarını yönetmek.
 * Kullanım Alanı: Hata yakalayıcılarda (Exception Handler), Kernel'de veya geliştirici tarafından herhangi bir yerde.
 * Bağımlılıklar: Psr\Log\LoggerInterface
 * Genişletilebilirlik: İleride Monolog gibi farklı sürücüleri yönetmek için `LogManagerInterface` eklenebilir.
 *
 * Örnek Kullanım:
 * $logger->error('Veritabanı bağlantısı koptu', ['context' => 'DB']);
 */
interface LoggerInterface extends PsrLoggerInterface
{
}
