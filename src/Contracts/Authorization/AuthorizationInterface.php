<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Authorization;

interface AuthorizationInterface
{
    public function can(string $ability, mixed $resource = null): bool;
    public function cannot(string $ability, mixed $resource = null): bool;
}
