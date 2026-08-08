<?php

declare(strict_types=1);

namespace Tests\Authorization;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Authorization\AccessDecisionManager;
use Palet\Framework\Authorization\AuthorizationContext;
use Palet\Framework\Authorization\RoleHierarchy;
use Palet\Framework\Authorization\PolicyEvaluator;
use Palet\Framework\Contracts\Authorization\PolicyInterface;

class AccessDecisionManagerTest extends TestCase
{
    public function test_access_granted_via_rbac()
    {
        $context = new AuthorizationContext(userId: 1);
        $hierarchy = new RoleHierarchy();
        $hierarchy->addInheritance('admin', 'editor');
        $evaluator = new PolicyEvaluator(); // No policies
        
        $manager = new AccessDecisionManager(
            $context,
            $hierarchy,
            $evaluator,
            ['admin'], // User has admin role
            ['editor' => ['edit_post']] // Editor role has edit_post permission
        );
        
        $this->assertTrue($manager->can('edit_post'));
    }

    public function test_access_denied_by_policy_even_if_rbac_allows()
    {
        $context = new AuthorizationContext(userId: 1, environment: 'production');
        $hierarchy = new RoleHierarchy();
        $evaluator = new PolicyEvaluator();
        
        // Add a policy that denies everything in production unless you are super-admin
        $policy = new class implements PolicyInterface {
            public function evaluate(mixed $context, string $ability, mixed $resource = null): ?bool {
                if ($context->environment === 'production' && $ability === 'delete_database') {
                    return false; 
                }
                return null;
            }
        };
        $evaluator->addPolicy($policy);
        
        $manager = new AccessDecisionManager(
            $context,
            $hierarchy,
            $evaluator,
            ['admin'], // User has admin role
            ['admin' => ['delete_database']] // Admin normally has this permission
        );
        
        $this->assertFalse($manager->can('delete_database'));
    }
}
