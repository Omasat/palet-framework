<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Resolution;

use Palet\Framework\Contracts\Tenancy\TenantResolverInterface;
use Palet\Framework\Contracts\Http\Message\RequestInterface;

class SubdomainTenantResolver implements TenantResolverInterface
{
    protected string $baseDomain;

    public function __construct(string $baseDomain)
    {
        $this->baseDomain = $baseDomain;
    }

    public function resolve(RequestInterface $request): string|int|null
    {
        $host = $request->getUri()->getHost();
        
        // e.g. host is "tenant1.example.com", baseDomain is "example.com"
        if (str_ends_with($host, '.' . $this->baseDomain)) {
            $subdomain = str_replace('.' . $this->baseDomain, '', $host);
            return $subdomain;
        }

        return null;
    }
}
