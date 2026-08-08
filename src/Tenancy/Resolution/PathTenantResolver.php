<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Resolution;

use Palet\Framework\Contracts\Tenancy\TenantResolverInterface;
use Palet\Framework\Contracts\Http\Message\RequestInterface;

class PathTenantResolver implements TenantResolverInterface
{
    protected int $segmentIndex;

    public function __construct(int $segmentIndex = 0)
    {
        $this->segmentIndex = $segmentIndex;
    }

    public function resolve(RequestInterface $request): string|int|null
    {
        $path = trim($request->getUri()->getPath(), '/');
        
        if (empty($path)) {
            return null;
        }

        $segments = explode('/', $path);
        
        if (isset($segments[$this->segmentIndex])) {
            return $segments[$this->segmentIndex];
        }

        return null;
    }
}
