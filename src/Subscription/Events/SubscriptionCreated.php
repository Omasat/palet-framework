<?php

declare(strict_types=1);

namespace Palet\Framework\Subscription\Events;

use Palet\Framework\Contracts\Subscription\SubscriptionInterface;

class SubscriptionCreated
{
    public function __construct(public readonly SubscriptionInterface $subscription) {}
}
