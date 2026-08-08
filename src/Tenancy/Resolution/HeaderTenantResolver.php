<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Resolution;

use Palet\Framework\Contracts\Tenancy\TenantResolverInterface;
use Palet\Framework\Contracts\Http\Message\RequestInterface;

class HeaderTenantResolver implements TenantResolverInterface
{
    protected string $headerName;

    public function __construct(string $headerName = 'X-Tenant-Id')
    {
        $this->headerName = $headerName;
    }

    public function resolve(RequestInterface $request): string|int|null
    {
        $headers = $request->getHeader($this->headerName);
        
        if (!empty($headers)) {
            return $headers[0];
        }

        return null;
    }
}
