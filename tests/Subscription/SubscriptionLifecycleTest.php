<?php

declare(strict_types=1);

namespace Tests\Subscription;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Subscription\SubscriptionLifecycleManager;
use Palet\Framework\Subscription\State\SubscriptionState;
use Palet\Framework\Contracts\Subscription\SubscriptionInterface;
use RuntimeException;
use DateTime;

class SubscriptionLifecycleTest extends TestCase
{
    protected function createDummySubscription(SubscriptionState $initialState)
    {
        return new class($initialState) implements SubscriptionInterface {
            public string $state;
            public function __construct(SubscriptionState $state) { $this->state = $state->value; }
            public function getId(): string|int { return 1; }
            public function getTenantId(): string|int { return 1; }
            public function getPlanId(): string|int { return 1; }
            public function getState(): string { return $this->state; }
            public function setState(string $state): void { $this->state = $state; }
            public function getExpiresAt(): ?\DateTimeInterface { return new DateTime('+1 month'); }
        };
    }

    public function test_valid_transitions()
    {
        $manager = new SubscriptionLifecycleManager();
        $sub = $this->createDummySubscription(SubscriptionState::DRAFT);
        
        $this->assertTrue($manager->canTransitionTo($sub, SubscriptionState::ACTIVE));
        $manager->transitionTo($sub, SubscriptionState::ACTIVE);
        $this->assertEquals(SubscriptionState::ACTIVE->value, $sub->getState());
        
        $manager->transitionTo($sub, SubscriptionState::GRACE_PERIOD);
        $this->assertEquals(SubscriptionState::GRACE_PERIOD->value, $sub->getState());
        
        $manager->transitionTo($sub, SubscriptionState::SUSPENDED);
        $this->assertEquals(SubscriptionState::SUSPENDED->value, $sub->getState());
    }

    public function test_invalid_transition_throws_exception()
    {
        $manager = new SubscriptionLifecycleManager();
        $sub = $this->createDummySubscription(SubscriptionState::EXPIRED);
        
        $this->expectException(RuntimeException::class);
        $manager->transitionTo($sub, SubscriptionState::GRACE_PERIOD);
    }
}
