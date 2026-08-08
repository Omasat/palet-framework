<?php

declare(strict_types=1);

namespace Palet\Framework\Authorization\Events;

class RoleCreated
{
    public function __construct(public readonly string $roleName) {}
}
