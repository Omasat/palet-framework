<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Authorization;

interface PolicyInterface
{
    /**
     * Evaluates if the policy allows the action.
     * Return true to explicitly allow.
     * Return false to explicitly deny.
     * Return null to skip and pass to next policy.
     */
    public function evaluate(mixed $context, string $ability, mixed $resource = null): ?bool;
}
