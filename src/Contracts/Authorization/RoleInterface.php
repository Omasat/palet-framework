<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Authorization;

interface RoleInterface
{
    public function getId(): string|int;
    public function getName(): string;
    
    /**
     * @return array<string>
     */
    public function getPermissions(): array;
}
