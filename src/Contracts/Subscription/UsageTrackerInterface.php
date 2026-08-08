<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Subscription;

interface UsageTrackerInterface
{
    public function recordUsage(string|int $subscriptionId, string $feature, int $amount = 1): void;
    public function getUsage(string|int $subscriptionId, string $feature): int|float;
    public function hasReachedLimit(string|int $subscriptionId, string $feature): bool;
}
