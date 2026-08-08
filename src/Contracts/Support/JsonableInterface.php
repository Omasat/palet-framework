<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Support;

interface JsonableInterface
{
    /**
     * Convert the object to its JSON representation.
     */
    public function toJson(int $options = 0): string;
}
