<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Resolution;

use Palet\Framework\Contracts\Tenancy\TenantResolverInterface;
use Palet\Framework\Contracts\Http\Message\RequestInterface;

class DomainTenantResolver implements TenantResolverInterface
{
    public function resolve(RequestInterface $request): string|int|null
    {
        $host = $request->getUri()->getHost();
        
        // E.g. host is "company-a.com"
        // Here we just return the host as the ID for demonstration.
        // In a real app, you might map this host to an ID in the DB.
        
        if (empty($host)) {
            return null;
        }

        return $host;
    }
}
