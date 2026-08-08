<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Support;

interface ArrayableInterface
{
    /**
     * Get the instance as an array.
     */
    public function toArray(): array;
}
