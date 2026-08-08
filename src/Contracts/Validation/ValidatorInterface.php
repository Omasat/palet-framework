<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Validation;

/**
 * Amaç: Gelen verilerin (genellikle HTTP isteklerinden) kurallara uygunluğunu denetler.
 * Sorumluluk: Kuralları işletmek, hatalı verileri tespit etmek ve hata mesajlarını (MessageBag) oluşturmak.
 * Kullanım Alanı: Form isteklerinde (Request), API girişlerinde ve veritabanı kayıt öncesi denetimlerde.
 * Bağımlılıklar: RuleInterface
 * Genişletilebilirlik: İhtiyaca göre özel doğrulama kuralı eklenebilir.
 *
 * Örnek Kullanım:
 * if ($validator->fails()) { return $validator->errors(); }
 */
interface ValidatorInterface
{
    /**
     * Determine if the data fails the validation rules.
     */
    public function fails(): bool;

    /**
     * Determine if the data passes the validation rules.
     */
    public function passes(): bool;

    /**
     * Get the failed validation rules.
     */
    public function failed(): array;

    /**
     * Get all of the validation error messages.
     */
    public function errors(): MessageBagInterface;

    /**
     * Run the validator's rules against its data.
     *
     * @throws \Exception When validation fails (optional based on implementation).
     */
    public function validate(): array;
}
