<?php

declare(strict_types=1);

namespace Tests\Subscription;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Subscription\Usage\UsageTracker;
use Palet\Framework\Contracts\Subscription\PlanInterface;
use RuntimeException;

class UsageTrackerTest extends TestCase
{
    protected function createPlan()
    {
        return new class implements PlanInterface {
            public function getId(): string|int { return 1; }
            public function getName(): string { return 'Pro'; }
            public function hasFeature(string $feature): bool {
                return $feature === 'users' || $feature === 'storage';
            }
            public function getFeatureLimit(string $feature): int|float|null {
                if ($feature === 'users') return 5;
                if ($feature === 'storage') return null; // unlimited
                return 0;
            }
        };
    }

    public function test_record_usage()
    {
        $tracker = new UsageTracker($this->createPlan());
        
        $tracker->recordUsage(1, 'users', 2);
        $this->assertEquals(2, $tracker->getUsage(1, 'users'));
        
        $tracker->recordUsage(1, 'users', 3);
        $this->assertEquals(5, $tracker->getUsage(1, 'users'));
        $this->assertTrue($tracker->hasReachedLimit(1, 'users'));
    }

    public function test_throws_exception_when_limit_reached()
    {
        $tracker = new UsageTracker($this->createPlan());
        $tracker->recordUsage(1, 'users', 5);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Feature limit reached for users. Limit: 5');
        
        $tracker->recordUsage(1, 'users', 1);
    }
    
    public function test_throws_exception_when_feature_not_in_plan()
    {
        $tracker = new UsageTracker($this->createPlan());
        
        $this->expectException(RuntimeException::class);
        $tracker->recordUsage(1, 'unauthorized_feature', 1);
    }
}
