<?php

declare(strict_types=1);

namespace Tests\Tenancy;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Tenancy\Resolution\DomainTenantResolver;
use Palet\Framework\Tenancy\Resolution\SubdomainTenantResolver;
use Palet\Framework\Tenancy\Resolution\HeaderTenantResolver;
use Palet\Framework\Tenancy\Resolution\PathTenantResolver;
use Palet\Framework\Http\Message\Request;
use Palet\Framework\Http\Message\Uri;

class ResolversTest extends TestCase
{
    public function test_domain_resolver()
    {
        $uri = (new Uri(''))->withHost('company-a.com');
        $request = (new Request('GET', $uri));
        
        $resolver = new DomainTenantResolver();
        $this->assertEquals('company-a.com', $resolver->resolve($request));
    }

    public function test_subdomain_resolver()
    {
        $uri = (new Uri(''))->withHost('tenant1.example.com');
        $request = (new Request('GET', $uri));
        
        $resolver = new SubdomainTenantResolver('example.com');
        $this->assertEquals('tenant1', $resolver->resolve($request));
    }
    
    public function test_subdomain_resolver_fails_on_wrong_domain()
    {
        $uri = (new Uri(''))->withHost('tenant1.other.com');
        $request = (new Request('GET', $uri));
        
        $resolver = new SubdomainTenantResolver('example.com');
        $this->assertNull($resolver->resolve($request));
    }

    public function test_header_resolver()
    {
        $request = (new Request('GET', '/'))->withHeader('X-Tenant-Id', '12345');
        
        $resolver = new HeaderTenantResolver('X-Tenant-Id');
        $this->assertEquals('12345', $resolver->resolve($request));
    }

    public function test_path_resolver()
    {
        $uri = (new Uri(''))->withPath('/api/v1/tenant-abc/users');
        $request = (new Request('GET', $uri));
        
        // Path segments: [api, v1, tenant-abc, users]
        // Segment 2 is 'tenant-abc'
        $resolver = new PathTenantResolver(2);
        $this->assertEquals('tenant-abc', $resolver->resolve($request));
    }
}
