<?php

declare(strict_types=1);

namespace Palet\Framework\Feature;

use Palet\Framework\Contracts\Feature\CapabilityInterface;
use Palet\Framework\Contracts\Feature\FeatureManagerInterface;

class CapabilityManager
{
    protected array $capabilities = [];

    public function __construct(protected FeatureManagerInterface $featureManager) {}

    public function register(CapabilityInterface $capability): void
    {
        $this->capabilities[$capability->getName()] = $capability;
    }

    public function hasCapability(string $name): bool
    {
        if (!isset($this->capabilities[$name])) {
            return false;
        }

        $capability = $this->capabilities[$name];
        
        foreach ($capability->getRequiredFeatures() as $feature) {
            if (!$this->featureManager->isActive($feature)) {
                return false;
            }
        }

        return true;
    }
}
