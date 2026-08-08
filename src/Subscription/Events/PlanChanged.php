<?php

declare(strict_types=1);

namespace Palet\Framework\Subscription\Events;

use Palet\Framework\Contracts\Subscription\SubscriptionInterface;
use Palet\Framework\Contracts\Subscription\PlanInterface;

class PlanChanged
{
    public function __construct(
        public readonly SubscriptionInterface $subscription,
        public readonly PlanInterface $oldPlan,
        public readonly PlanInterface $newPlan
    ) {}
}
