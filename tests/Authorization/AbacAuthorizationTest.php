<?php

declare(strict_types=1);

namespace Tests\Authorization;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Authorization\PolicyEvaluator;
use Palet\Framework\Contracts\Authorization\PolicyInterface;
use Palet\Framework\Authorization\AuthorizationContext;

class AbacAuthorizationTest extends TestCase
{
    public function test_policy_evaluator_returns_explicit_allow()
    {
        $evaluator = new PolicyEvaluator();
        
        $policy = new class implements PolicyInterface {
            public function evaluate(mixed $context, string $ability, mixed $resource = null): ?bool {
                if ($context->ipAddress === '127.0.0.1') {
                    return true;
                }
                return null;
            }
        };
        
        $evaluator->addPolicy($policy);
        
        $context = new AuthorizationContext(userId: 1, ipAddress: '127.0.0.1');
        
        $this->assertTrue($evaluator->evaluate($context, 'view_dashboard'));
    }

    public function test_policy_evaluator_returns_explicit_deny()
    {
        $evaluator = new PolicyEvaluator();
        
        $policy = new class implements PolicyInterface {
            public function evaluate(mixed $context, string $ability, mixed $resource = null): ?bool {
                if ($context->tenantId !== $resource->tenantId) {
                    return false; // Cross-tenant access denied!
                }
                return null;
            }
        };
        
        $evaluator->addPolicy($policy);
        
        $context = new AuthorizationContext(userId: 1, tenantId: 'tenant-a');
        $resource = (object)['tenantId' => 'tenant-b'];
        
        $this->assertFalse($evaluator->evaluate($context, 'edit_document', $resource));
    }
}
