<?php

declare(strict_types=1);

namespace Tests\Package;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Package\DependencyResolver;
use Palet\Framework\Package\SemVer;
use Palet\Framework\Contracts\Package\PackageRepositoryInterface;

class MockRepository implements PackageRepositoryInterface
{
    protected array $packages = [
        'palet/router' => [
            '1.0.0' => ['dependencies' => []],
            '1.1.0' => ['dependencies' => []],
            '2.0.0' => ['dependencies' => []],
        ],
        'palet/framework' => [
            '1.0.0' => ['dependencies' => ['palet/router' => '^1.0.0']],
        ],
        'palet/conflict' => [
            '1.0.0' => ['dependencies' => ['palet/router' => '^1.0.0', 'palet/other' => '^1.0.0']],
        ],
        'palet/other' => [
            '1.0.0' => ['dependencies' => ['palet/router' => '^2.0.0']],
        ],
        'palet/circular-a' => [
            '1.0.0' => ['dependencies' => ['palet/circular-b' => '^1.0.0']],
        ],
        'palet/circular-b' => [
            '1.0.0' => ['dependencies' => ['palet/circular-a' => '^1.0.0']],
        ]
    ];

    public function find(string $packageName): ?array
    {
        return isset($this->packages[$packageName]) ? $this->packages[$packageName] : null;
    }
    
    public function getVersions(string $packageName): array
    {
        return isset($this->packages[$packageName]) ? array_keys($this->packages[$packageName]) : [];
    }
}

class DependencyResolverTest extends TestCase
{
    public function test_resolves_simple_dependency()
    {
        $resolver = new DependencyResolver(new MockRepository());
        
        $resolved = $resolver->resolve('palet/framework', '^1.0.0');
        
        $this->assertArrayHasKey('palet/framework', $resolved);
        $this->assertEquals('1.0.0', $resolved['palet/framework']['version']);
        
        $this->assertArrayHasKey('palet/router', $resolved);
        $this->assertEquals('1.1.0', $resolved['palet/router']['version']); // Highest matching ^1.0.0
    }

    public function test_throws_on_circular_dependency()
    {
        $resolver = new DependencyResolver(new MockRepository());
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Circular dependency detected');
        
        $resolver->resolve('palet/circular-a', '^1.0.0');
    }
}
