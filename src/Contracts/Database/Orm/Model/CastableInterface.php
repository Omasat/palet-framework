<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm\Model;

interface CastableInterface
{
    /**
     * Determine whether an attribute should be cast to a native type.
     */
    public function hasCast(string $key): bool;

    /**
     * Cast an attribute to a native PHP type.
     */
    public function castAttribute(string $key, mixed $value): mixed;
}
