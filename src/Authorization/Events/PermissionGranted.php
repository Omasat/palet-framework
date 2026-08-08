<?php

declare(strict_types=1);

namespace Palet\Framework\Authorization\Events;

class PermissionGranted
{
    public function __construct(
        public readonly string $roleName,
        public readonly string $permissionName
    ) {}
}
