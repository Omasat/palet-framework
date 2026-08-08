<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy\Provisioning;

use Closure;
use Exception;

class TenantProvisionPipeline
{
    protected array $pipes = [];

    public function pipe(callable $pipe): static
    {
        $this->pipes[] = $pipe;
        return $this;
    }

    public function process(TenantProvisionContext $context): TenantProvisionContext
    {
        $index = 0;
        $pipes = $this->pipes;

        $next = function (TenantProvisionContext $ctx) use (&$index, $pipes, &$next) {
            if (!isset($pipes[$index])) {
                return $ctx;
            }

            $pipe = $pipes[$index++];
            return $pipe($ctx, $next);
        };

        try {
            return $next($context);
        } catch (Exception $e) {
            // Trigger rollback mechanism if needed.
            throw $e;
        }
    }
}
