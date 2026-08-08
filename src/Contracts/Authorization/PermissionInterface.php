<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Authorization;

interface PermissionInterface
{
    public function getName(): string;
    public function getDescription(): string;
}
