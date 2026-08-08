<?php

declare(strict_types=1);

namespace Palet\Framework\Translation\Events;

class TranslationMissing
{
    public readonly string $key;
    public readonly string $locale;

    public function __construct(string $key, string $locale)
    {
        $this->key = $key;
        $this->locale = $locale;
    }
}
