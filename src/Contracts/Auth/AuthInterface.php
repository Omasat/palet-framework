<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Auth;

/**
 * Amaç: Birden fazla kimlik doğrulama mekanizmasını (Guard) yönetir.
 * Sorumluluk: Uygulama içerisinde tanımlanmış farklı doğrulama yöntemlerine (Session, Token, JWT) erişimi sağlamak.
 * Kullanım Alanı: Facade veya Dependency Injection ile çoklu Auth erişimlerinde.
 * Bağımlılıklar: GuardInterface
 * Genişletilebilirlik: Dinamik olarak yeni Guard'lar eklenebilir.
 *
 * Örnek Kullanım:
 * $auth->guard('api')->user();
 */
interface AuthInterface
{
    /**
     * Get a guard instance by name.
     */
    public function guard(?string $name = null): GuardInterface;
}
