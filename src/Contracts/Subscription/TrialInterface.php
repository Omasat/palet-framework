<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Subscription;

interface TrialInterface
{
    public function isEligible(string|int $tenantId): bool;
    public function startTrial(string|int $tenantId, PlanInterface $plan): SubscriptionInterface;
    public function endTrial(string|int $subscriptionId): void;
}
