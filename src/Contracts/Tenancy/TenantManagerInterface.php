<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Tenancy;

use Palet\Framework\Contracts\Http\Message\RequestInterface;

interface TenantManagerInterface
{
    /**
     * Initializes the tenant context for the current request.
     */
    public function initialize(RequestInterface $request): void;
    
    /**
     * Ends the tenant context and cleans up.
     */
    public function endContext(): void;
}
