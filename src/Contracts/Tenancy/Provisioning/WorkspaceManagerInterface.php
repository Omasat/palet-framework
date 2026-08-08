<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Tenancy\Provisioning;

use Palet\Framework\Contracts\Tenancy\TenantInterface;

interface WorkspaceManagerInterface
{
    public function create(TenantInterface $tenant): void;
    public function activate(TenantInterface $tenant): void;
    public function suspend(TenantInterface $tenant): void;
    public function archive(TenantInterface $tenant): void;
    public function restore(TenantInterface $tenant): void;
    public function delete(TenantInterface $tenant): void;
}
