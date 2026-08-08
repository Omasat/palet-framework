<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Tenancy;

interface TenantInterface
{
    public function getId(): string|int;
    public function getDomain(): string;
    public function getDatabaseConfig(): array;
    public function getCachePrefix(): string;
}
