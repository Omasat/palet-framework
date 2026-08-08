<?php

declare(strict_types=1);

namespace Palet\Framework\Translation\Formatters;

use Palet\Framework\Contracts\Translation\FormatterInterface;

class CurrencyFormatter implements FormatterInterface
{
    public function format(mixed $value, string $locale, array $options = []): string
    {
        $currency = $options['currency'] ?? 'USD';

        if (!class_exists('NumberFormatter')) {
            // Fallback if intl extension is not installed
            $numberFormatter = new NumberFormatter();
            $formatted = $numberFormatter->format($value, $locale, $options);
            return $currency . ' ' . $formatted; // simple fallback
        }

        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        
        return $formatter->formatCurrency((float) $value, $currency);
    }
}
