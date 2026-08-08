<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Config;

/**
 * Amaç: Çevresel değişkenleri (Environment Variables) okuma ve yönetme standartlarını belirler.
 * Sorumluluk: .env veya sistem değişkenlerini güvenli şekilde uygulamaya sunmak.
 * Kullanım Alanı: Config katmanı yüklenmeden önce ve Framework boot aşamasında.
 * Bağımlılıklar: Yok
 * Genişletilebilirlik: İleride AWS Secrets Manager veya HashiCorp Vault adaptörleri eklenebilir.
 *
 * Örnek Kullanım:
 * $dbHost = $env->get('DB_HOST', '127.0.0.1');
 */
interface EnvironmentInterface
{
    /**
     * Get the value of an environment variable.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Determine if an environment variable exists.
     */
    public function has(string $key): bool;
}
