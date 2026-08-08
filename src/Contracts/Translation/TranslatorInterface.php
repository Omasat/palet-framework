<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Translation;

interface TranslatorInterface
{
    /**
     * Get the translation for a given key.
     */
    public function get(string $key, array $replace = [], ?string $locale = null): string|array|null;

    /**
     * Get a translation according to an integer value (pluralization).
     */
    public function choice(string $key, int|float|array|\Countable $number, array $replace = [], ?string $locale = null): string;

    /**
     * Determine if a translation exists.
     */
    public function has(string $key, ?string $locale = null, bool $fallback = true): bool;

    /**
     * Get the default locale being used.
     */
    public function getLocale(): string;

    /**
     * Set the default locale.
     */
    public function setLocale(string $locale): void;

    /**
     * Get the fallback locale being used.
     */
    public function getFallback(): string;

    /**
     * Set the fallback locale being used.
     */
    public function setFallback(string $fallback): void;
}
