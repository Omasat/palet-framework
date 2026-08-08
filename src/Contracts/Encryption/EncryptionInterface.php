<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Encryption;

/**
 * Amaç: Hassas verilerin güvenli bir şekilde şifrelenmesi ve çözülmesini sağlar.
 * Sorumluluk: OpenSSL veya Sodium gibi şifreleme algoritmaları üzerinden çift yönlü (two-way) şifreleme yapmak.
 * Kullanım Alanı: Cookie şifrelemesi, Session verilerinin şifrelenmesi veya veritabanındaki özel alanlarda.
 * Bağımlılıklar: Yok
 * Genişletilebilirlik: Gelecekte asimetrik (public/private key) şifreleme arayüzleri eklenebilir.
 *
 * Örnek Kullanım:
 * $encrypted = $encrypter->encrypt('secret');
 * $decrypted = $encrypter->decrypt($encrypted);
 */
interface EncryptionInterface
{
    /**
     * Encrypt the given value.
     *
     * @throws \Exception
     */
    public function encrypt(mixed $value, bool $serialize = true): string;

    /**
     * Decrypt the given value.
     *
     * @throws \Exception
     */
    public function decrypt(string $payload, bool $unserialize = true): mixed;
}
