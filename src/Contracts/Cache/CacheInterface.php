<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Cache;

/**
 * Amaç: Birden fazla önbellek deposunu (Cache Manager) yönetir.
 * Sorumluluk: Uygulama içerisinde tanımlanmış farklı cache sürücülerine (redis, memcached, file) erişimi sağlamak.
 * Kullanım Alanı: Facade veya Dependency Injection ile çoklu önbellek erişimlerinde.
 * Bağımlılıklar: CacheStoreInterface
 * Genişletilebilirlik: İhtiyaca göre dinamik sürücü (driver) ekleme yeteneği getirilebilir.
 *
 * Örnek Kullanım:
 * $cache->store('redis')->get('key');
 */
interface CacheInterface
{
    /**
     * Get a cache store instance by name.
     */
    public function store(?string $name = null): CacheStoreInterface;
}
