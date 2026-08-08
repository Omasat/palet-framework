<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Provisioning;

class TenantProvisionContext
{
    public array $data = [];
    public ?string $tenantId = null;
    public bool $isSuccess = false;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
