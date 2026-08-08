<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Config;

/**
 * Amaç: Uygulamanın tüm konfigürasyon (ayar) değerlerini yönetmek.
 * Sorumluluk: Ayar dosyalarını yüklemek, okumak ve gerekirse çalışma anında (runtime) değerleri geçici olarak değiştirmek.
 * Kullanım Alanı: Framework'ün her katmanında (veritabanı, önbellek, güvenlik vb. ayarlarını alırken).
 * Bağımlılıklar: Yok
 * Genişletilebilirlik: İleride veritabanı tabanlı (Database Config) yapılandırma sürücüleri eklenebilir.
 *
 * Örnek Kullanım:
 * $dbConfig = $config->get('database.connections.mysql');
 */
interface ConfigInterface
{
    /**
     * Determine if the given configuration value exists.
     */
    public function has(string $key): bool;

    /**
     * Get the specified configuration value.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Set a given configuration value.
     */
    public function set(string $key, mixed $value): void;
}
