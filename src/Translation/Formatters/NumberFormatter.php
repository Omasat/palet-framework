<?php

declare(strict_types=1);

namespace Palet\Framework\Translation\Formatters;

use Palet\Framework\Contracts\Translation\FormatterInterface;

class NumberFormatter implements FormatterInterface
{
    public function format(mixed $value, string $locale, array $options = []): string
    {
        if (!class_exists('NumberFormatter')) {
            // Fallback if intl extension is not installed
            $decimals = $options['decimals'] ?? 2;
            $dec_point = $locale === 'tr' || str_starts_with($locale, 'tr_') ? ',' : '.';
            $thousands_sep = $dec_point === ',' ? '.' : ',';
            return number_format((float) $value, $decimals, $dec_point, $thousands_sep);
        }

        $style = $options['style'] ?? \NumberFormatter::DECIMAL;
        $formatter = new \NumberFormatter($locale, $style);

        if (isset($options['decimals'])) {
            $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $options['decimals']);
        }

        return $formatter->format((float) $value);
    }
}
