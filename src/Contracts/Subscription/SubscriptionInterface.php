<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Subscription;

interface SubscriptionInterface
{
    public function getId(): string|int;
    public function getTenantId(): string|int;
    public function getPlanId(): string|int;
    public function getState(): string;
    public function getExpiresAt(): ?\DateTimeInterface;
}
