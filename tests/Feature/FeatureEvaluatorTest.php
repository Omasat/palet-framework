<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Feature\FeatureEvaluator;
use Palet\Framework\Feature\State\FeatureState;
use Palet\Framework\Contracts\Feature\FeatureFlagInterface;

class FeatureEvaluatorTest extends TestCase
{
    protected function createDummyFlag(string $name, FeatureState $state)
    {
        return new class($name, $state) implements FeatureFlagInterface {
            public function __construct(private string $name, private FeatureState $state) {}
            public function getName(): string { return $this->name; }
            public function getType(): string { return 'boolean'; }
            public function getState(): string { return $this->state->value; }
            public function getPolicies(): array { return []; }
        };
    }

    public function test_evaluates_enabled_feature()
    {
        $evaluator = new FeatureEvaluator();
        $feature = $this->createDummyFlag('test_feature', FeatureState::ENABLED);
        
        $this->assertTrue($evaluator->resolve($feature, null));
    }

    public function test_evaluates_disabled_feature()
    {
        $evaluator = new FeatureEvaluator();
        $feature = $this->createDummyFlag('test_feature', FeatureState::DISABLED);
        
        $this->assertFalse($evaluator->resolve($feature, null));
    }

    public function test_evaluates_with_policies()
    {
        $evaluator = new FeatureEvaluator();
        
        // Add a policy that returns true if context is "admin"
        $evaluator->addPolicy(function(FeatureFlagInterface $feature, mixed $context) {
            if ($context === 'admin') {
                return true;
            }
            return null; // pass to next policy or default state
        });
        
        // Base state is draft (normally false)
        $feature = $this->createDummyFlag('beta_feature', FeatureState::SCHEDULED);
        
        $this->assertTrue($evaluator->resolve($feature, 'admin'));
        // Without policy matching, falls back to scheduled (enabled/disabled logic handled properly, here default false if not explicitly enabled)
        $this->assertFalse($evaluator->resolve($feature, 'guest'));
    }
}
