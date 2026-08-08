<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Events;

use Palet\Framework\Contracts\Http\Message\RequestInterface;

class TenantResolving
{
    public function __construct(public readonly RequestInterface $request) {}
}
