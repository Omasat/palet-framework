<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Feature\CapabilityManager;
use Palet\Framework\Feature\FeatureManifest;
use Palet\Framework\Contracts\Feature\FeatureManagerInterface;

class CapabilityManagerTest extends TestCase
{
    public function test_has_capability_returns_true_when_all_features_active()
    {
        $featureManager = new class implements FeatureManagerInterface {
            public function isActive(string $feature): bool {
                return $feature === 'feature_a' || $feature === 'feature_b';
            }
            public function enable(string $feature): void {}
            public function disable(string $feature): void {}
        };
        
        $capabilityManager = new CapabilityManager($featureManager);
        
        $manifest = new FeatureManifest('HospitalModule', 'Hospital Management System', ['feature_a', 'feature_b']);
        $capabilityManager->register($manifest);
        
        $this->assertTrue($capabilityManager->hasCapability('HospitalModule'));
    }

    public function test_has_capability_returns_false_when_feature_inactive()
    {
        $featureManager = new class implements FeatureManagerInterface {
            public function isActive(string $feature): bool {
                return $feature === 'feature_a'; // feature_b is inactive
            }
            public function enable(string $feature): void {}
            public function disable(string $feature): void {}
        };
        
        $capabilityManager = new CapabilityManager($featureManager);
        
        $manifest = new FeatureManifest('HospitalModule', 'Hospital Management System', ['feature_a', 'feature_b']);
        $capabilityManager->register($manifest);
        
        $this->assertFalse($capabilityManager->hasCapability('HospitalModule'));
    }
}
