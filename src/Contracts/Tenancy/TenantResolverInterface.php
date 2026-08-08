<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Tenancy;

use Palet\Framework\Contracts\Http\Message\RequestInterface;

interface TenantResolverInterface
{
    /**
     * Tries to resolve a tenant identifier from the request.
     * Returns the tenant ID or null if not found.
     */
    public function resolve(RequestInterface $request): string|int|null;
}
