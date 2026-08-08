<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Auth;

/**
 * Amaç: Kullanıcı bilgilerini çeşitli depolardan (Database, LDAP, API) getirmeyi standartlaştırır.
 * Sorumluluk: Kimlik ID'si veya kimlik bilgilerine (credentials) göre kullanıcı nesnesini (User) bulmak.
 * Kullanım Alanı: Guard sınıflarının içerisindeki veritabanı/depo iletişimini soyutlamak için kullanılır.
 * Bağımlılıklar: Yok
 * Genişletilebilirlik: Eloquent, Doctrine veya MongoDB tabanlı sağlayıcılar eklenebilir.
 *
 * Örnek Kullanım:
 * $user = $provider->retrieveById($id);
 */
interface UserProviderInterface
{
    /**
     * Retrieve a user by their unique identifier.
     */
    public function retrieveById(mixed $identifier): mixed;

    /**
     * Retrieve a user by the given credentials.
     */
    public function retrieveByCredentials(array $credentials): mixed;

    /**
     * Validate a user against the given credentials.
     */
    public function validateCredentials(mixed $user, array $credentials): bool;
}
