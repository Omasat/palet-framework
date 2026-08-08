<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Subscription;

interface PlanInterface
{
    public function getId(): string|int;
    public function getName(): string;
    
    /**
     * Checks if a specific feature is enabled for this plan.
     */
    public function hasFeature(string $feature): bool;

    /**
     * Gets the limit for a specific feature (e.g. max_users = 10).
     */
    public function getFeatureLimit(string $feature): int|float|null;
}
