<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Translation;

interface FormatterInterface
{
    /**
     * Format a value according to the given locale and options.
     */
    public function format(mixed $value, string $locale, array $options = []): string;
}
