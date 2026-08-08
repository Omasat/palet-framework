<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Cache;

use Psr\SimpleCache\CacheInterface as PsrSimpleCacheInterface;

/**
 * Amaç: Seçili bir önbellek deposu (store) üzerinde veri okuma ve yazma işlemlerini yönetir.
 * Sorumluluk: PSR-16 SimpleCache uyumluluğunu sürdürerek uygulamanın temel cache ihtiyaçlarını karşılamak.
 * Kullanım Alanı: Sık kullanılan sorguları, HTML çıktısını veya API yanıtlarını bellekte tutmak için.
 * Bağımlılıklar: Psr\SimpleCache\CacheInterface
 * Genişletilebilirlik: İhtiyaca göre tag'leme (Etiket) mekanizması eklenmiş bir TaggedCacheInterface oluşturulabilir.
 *
 * Örnek Kullanım:
 * $cache->set('key', 'value', 3600);
 */
interface CacheStoreInterface extends PsrSimpleCacheInterface
{
    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     */
    public function remember(string $key, int $ttl, \Closure $callback): mixed;
}
