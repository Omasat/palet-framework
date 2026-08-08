<?php

declare(strict_types=1);

namespace Palet\Framework\Subscription;

use Palet\Framework\Contracts\Subscription\SubscriptionInterface;
use Palet\Framework\Subscription\State\SubscriptionState;
use RuntimeException;

class SubscriptionLifecycleManager
{
    protected array $transitions = [
        SubscriptionState::DRAFT->value => [
            SubscriptionState::TRIAL,
            SubscriptionState::ACTIVE,
            SubscriptionState::CANCELLED
        ],
        SubscriptionState::TRIAL->value => [
            SubscriptionState::ACTIVE,
            SubscriptionState::EXPIRED,
            SubscriptionState::CANCELLED
        ],
        SubscriptionState::ACTIVE->value => [
            SubscriptionState::GRACE_PERIOD,
            SubscriptionState::SUSPENDED,
            SubscriptionState::EXPIRED,
            SubscriptionState::CANCELLED
        ],
        SubscriptionState::GRACE_PERIOD->value => [
            SubscriptionState::ACTIVE,
            SubscriptionState::SUSPENDED,
            SubscriptionState::EXPIRED
        ],
        SubscriptionState::SUSPENDED->value => [
            SubscriptionState::ACTIVE,
            SubscriptionState::CANCELLED
        ],
        SubscriptionState::EXPIRED->value => [
            SubscriptionState::ACTIVE
        ],
        SubscriptionState::CANCELLED->value => [
            // Usually terminal, maybe allow draft or active for reactivation
        ]
    ];

    public function transitionTo(SubscriptionInterface $subscription, SubscriptionState $newState): void
    {
        $currentState = SubscriptionState::tryFrom($subscription->getState()) ?? SubscriptionState::DRAFT;
        
        if (!$this->canTransitionTo($subscription, $newState)) {
            throw new RuntimeException("Cannot transition subscription from {$currentState->value} to {$newState->value}.");
        }

        if (method_exists($subscription, 'setState')) {
            $subscription->setState($newState->value);
        }
    }

    public function canTransitionTo(SubscriptionInterface $subscription, SubscriptionState $newState): bool
    {
        $currentState = SubscriptionState::tryFrom($subscription->getState()) ?? SubscriptionState::DRAFT;
        $allowed = $this->transitions[$currentState->value] ?? [];
        
        return in_array($newState, $allowed, true);
    }
}
