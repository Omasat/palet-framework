<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Validation;

/**
 * Amaç: Tek bir doğrulama kuralını temsil eder.
 * Sorumluluk: Belirli bir verinin şarta uyup uymadığını sınamak ve uymazsa hata mesajı dönmek.
 * Kullanım Alanı: Özel doğrulama kuralları (Custom Validation Rules) oluştururken uygulanır.
 * Bağımlılıklar: Yok
 * Genişletilebilirlik: Karmaşık iş kuralları için bu arayüzden sayısız kural türetilebilir.
 *
 * Örnek Kullanım:
 * class UppercaseRule implements RuleInterface { ... }
 */
interface RuleInterface
{
    /**
     * Determine if the validation rule passes.
     */
    public function passes(string $attribute, mixed $value): bool;

    /**
     * Get the validation error message.
     */
    public function message(): string;
}
