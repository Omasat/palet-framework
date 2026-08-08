<?php

declare(strict_types=1);

namespace Palet\Framework\Subscription\Usage;

use Palet\Framework\Contracts\Subscription\UsageTrackerInterface;
use Palet\Framework\Contracts\Subscription\PlanInterface;
use RuntimeException;

class UsageTracker implements UsageTrackerInterface
{
    protected array $usages = [];
    protected PlanInterface $plan;

    public function __construct(PlanInterface $plan)
    {
        $this->plan = $plan;
    }

    public function recordUsage(string|int $subscriptionId, string $feature, int $amount = 1): void
    {
        if (!$this->plan->hasFeature($feature)) {
            throw new RuntimeException("Feature {$feature} is not available in the current plan.");
        }

        $current = $this->getUsage($subscriptionId, $feature);
        $newAmount = $current + $amount;
        
        $limit = $this->plan->getFeatureLimit($feature);
        
        if ($limit !== null && $newAmount > $limit) {
            throw new RuntimeException("Feature limit reached for {$feature}. Limit: {$limit}");
        }

        $this->usages[$subscriptionId][$feature] = $newAmount;
    }

    public function getUsage(string|int $subscriptionId, string $feature): int|float
    {
        return $this->usages[$subscriptionId][$feature] ?? 0;
    }

    public function hasReachedLimit(string|int $subscriptionId, string $feature): bool
    {
        $limit = $this->plan->getFeatureLimit($feature);
        if ($limit === null) {
            return false;
        }
        
        return $this->getUsage($subscriptionId, $feature) >= $limit;
    }
}
