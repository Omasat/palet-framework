<?php

declare(strict_types=1);

namespace Palet\Framework\Authorization\Events;

use Palet\Framework\Contracts\Authorization\PolicyInterface;

class PolicyEvaluated
{
    public function __construct(
        public readonly PolicyInterface $policy,
        public readonly ?bool $result
    ) {}
}
