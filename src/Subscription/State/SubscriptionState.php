<?php

declare(strict_types=1);

namespace Palet\Framework\Subscription\State;

enum SubscriptionState: string
{
    case DRAFT = 'draft';
    case TRIAL = 'trial';
    case ACTIVE = 'active';
    case GRACE_PERIOD = 'grace_period';
    case SUSPENDED = 'suspended';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
}
