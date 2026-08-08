<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Feature\RuntimeFeatureContext;

class RuntimeFeatureContextTest extends TestCase
{
    public function test_context_holds_data()
    {
        $context = new RuntimeFeatureContext(
            environment: 'production',
            tenantId: 'tenant-1',
            planId: 'pro',
            role: 'admin',
            userId: 'user-123'
        );
        
        $this->assertEquals('production', $context->environment);
        $this->assertEquals('tenant-1', $context->tenantId);
        $this->assertEquals('pro', $context->planId);
        $this->assertEquals('admin', $context->role);
        $this->assertEquals('user-123', $context->userId);
    }
}
