<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Session;

/**
 * Amaç: Kullanıcının oturum (session) verilerini yönetmek.
 * Sorumluluk: Oturum başlatmak, veri yazmak/okumak, flash veriler oluşturmak ve oturumu yok etmek.
 * Kullanım Alanı: Kimlik doğrulama, sepet işlemleri ve kullanıcı tercihleri için.
 * Bağımlılıklar: Yok
 * Genişletilebilirlik: Veritabanı veya Redis tabanlı session sürücüleri yazılabilir.
 *
 * Örnek Kullanım:
 * $session->put('user_id', 5);
 */
interface SessionInterface
{
    /**
     * Start the session.
     */
    public function start(): bool;

    /**
     * Determine if an item exists in the session.
     */
    public function has(string $key): bool;

    /**
     * Get an item from the session.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Put a key / value pair or array of key / value pairs in the session.
     */
    public function put(string|array $key, mixed $value = null): void;

    /**
     * Remove an item from the session.
     */
    public function forget(string|array $keys): void;

    /**
     * Remove all of the items from the session.
     */
    public function flush(): void;

    /**
     * Get the current session ID.
     */
    public function getId(): string;

    /**
     * Set a new, secure session ID.
     */
    public function regenerate(bool $destroy = false): bool;

    /**
     * Save the session data to storage.
     */
    public function save(): void;

    /**
     * Flash a key / value pair to the session.
     */
    public function flash(string $key, mixed $value = true): void;

    /**
     * Flash a key / value pair to the session for immediate use.
     */
    public function now(string $key, mixed $value = true): void;

    /**
     * Reflash all of the session flash data.
     */
    public function reflash(): void;

    /**
     * Reflash a subset of the current flash data.
     */
    public function keep(mixed $keys = null): void;
}
