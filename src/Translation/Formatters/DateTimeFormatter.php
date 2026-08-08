<?php

declare(strict_types=1);

namespace Palet\Framework\Translation\Formatters;

use Palet\Framework\Contracts\Translation\FormatterInterface;
use DateTimeInterface;

class DateTimeFormatter implements FormatterInterface
{
    public function format(mixed $value, string $locale, array $options = []): string
    {
        if (!$value instanceof DateTimeInterface) {
            $value = new \DateTime((string) $value);
        }

        if (!class_exists('IntlDateFormatter')) {
            // Fallback if intl extension is not installed
            return $value->format($options['format'] ?? 'Y-m-d H:i:s');
        }

        $dateType = $options['date_type'] ?? \IntlDateFormatter::MEDIUM;
        $timeType = $options['time_type'] ?? \IntlDateFormatter::SHORT;
        $timezone = $options['timezone'] ?? $value->getTimezone();

        $formatter = new \IntlDateFormatter(
            $locale,
            $dateType,
            $timeType,
            $timezone
        );

        if (isset($options['pattern'])) {
            $formatter->setPattern($options['pattern']);
        }

        return $formatter->format($value);
    }
}
