<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Authorization;

/**
 * Amaç: Yetkilendirme (Authorization) kurallarının merkezi yöneticisidir.
 * Sorumluluk: Kullanıcının belirli bir işlemi (Ability) belirli bir model üzerinde yapıp yapamayacağını kontrol etmek.
 * Kullanım Alanı: Middleware'lerde, Controller'larda veya View (Blade) katmanında.
 * Bağımlılıklar: Yok
 * Genişletilebilirlik: 'Before' ve 'After' kancaları (hooks) eklenebilir.
 *
 * Örnek Kullanım:
 * if ($gate->allows('update', $post)) { ... }
 */
interface GateInterface
{
    /**
     * Determine if a given ability has been defined.
     */
    public function has(string $ability): bool;

    /**
     * Define a new ability.
     */
    public function define(string $ability, callable|string $callback): self;

    /**
     * Determine if the given ability should be granted for the current user.
     */
    public function allows(string $ability, mixed $arguments = []): bool;

    /**
     * Determine if the given ability should be denied for the current user.
     */
    public function denies(string $ability, mixed $arguments = []): bool;
}
