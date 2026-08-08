<?php

declare(strict_types=1);

namespace Tests\Tenancy\Provisioning;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Tenancy\Provisioning\TenantProvisionPipeline;
use Palet\Framework\Tenancy\Provisioning\TenantProvisionContext;
use Exception;

class ProvisioningPipelineTest extends TestCase
{
    public function test_pipeline_executes_in_order()
    {
        $pipeline = new TenantProvisionPipeline();
        
        $pipeline->pipe(function (TenantProvisionContext $ctx, callable $next) {
            $ctx->data['step1'] = true;
            return $next($ctx);
        });
        
        $pipeline->pipe(function (TenantProvisionContext $ctx, callable $next) {
            $ctx->data['step2'] = true;
            $ctx->isSuccess = true;
            return $next($ctx);
        });
        
        $context = new TenantProvisionContext(['initial' => true]);
        $result = $pipeline->process($context);
        
        $this->assertTrue($result->isSuccess);
        $this->assertTrue($result->data['initial']);
        $this->assertTrue($result->data['step1']);
        $this->assertTrue($result->data['step2']);
    }

    public function test_pipeline_halts_on_exception()
    {
        $pipeline = new TenantProvisionPipeline();
        
        $pipeline->pipe(function (TenantProvisionContext $ctx, callable $next) {
            $ctx->data['step1'] = true;
            return $next($ctx);
        });
        
        $pipeline->pipe(function (TenantProvisionContext $ctx, callable $next) {
            throw new Exception("Simulated failure in step 2");
        });
        
        $pipeline->pipe(function (TenantProvisionContext $ctx, callable $next) {
            $ctx->data['step3'] = true;
            return $next($ctx);
        });
        
        $context = new TenantProvisionContext([]);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Simulated failure in step 2");
        
        $pipeline->process($context);
    }
}
