<?php

declare(strict_types=1);

namespace Palet\Framework\Translation\Events;

class LocaleChanged
{
    public readonly string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }
}
