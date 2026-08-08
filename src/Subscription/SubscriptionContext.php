<?php

declare(strict_types=1);

namespace Palet\Framework\Subscription;

use Palet\Framework\Contracts\Subscription\SubscriptionInterface;

class SubscriptionContext
{
    protected ?SubscriptionInterface $subscription = null;

    public function setSubscription(SubscriptionInterface $subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getSubscription(): ?SubscriptionInterface
    {
        return $this->subscription;
    }

    public function hasSubscription(): bool
    {
        return $this->subscription !== null;
    }
}
