<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Auth;

/**
 * Amaç: Belirli bir yöntemle (Session, Token vb.) kimlik doğrulaması yapan mekanizmayı temsil eder.
 * Sorumluluk: Kullanıcının giriş yapıp yapmadığını kontrol etmek, geçerli kullanıcıyı getirmek.
 * Kullanım Alanı: Auth Middleware'lerinde veya Controller'larda yetki kontrolleri için.
 * Bağımlılıklar: Yok
 * Genişletilebilirlik: Stateful (Session) veya Stateless (Token) olmak üzere alt arayüzler türetilebilir.
 *
 * Örnek Kullanım:
 * if ($guard->check()) { $user = $guard->user(); }
 */
interface GuardInterface
{
    /**
     * Determine if the current user is authenticated.
     */
    public function check(): bool;

    /**
     * Determine if the current user is a guest.
     */
    public function guest(): bool;

    /**
     * Get the currently authenticated user.
     */
    public function user(): mixed;

    /**
     * Get the ID for the currently authenticated user.
     */
    public function id(): mixed;

    /**
     * Validate a user's credentials.
     */
    public function validate(array $credentials = []): bool;
}
