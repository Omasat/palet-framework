<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Authorization;

interface AuthorizableInterface
{
    /**
     * Determine if the entity has a given ability.
     */
    public function can(string $ability, mixed $arguments = []): bool;

    /**
     * Determine if the entity does not have a given ability.
     */
    public function cannot(string $ability, mixed $arguments = []): bool;
}
