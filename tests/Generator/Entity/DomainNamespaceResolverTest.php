<?php

declare(strict_types=1);

namespace Tests\Generator\Entity;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Generator\Entity\DomainNamespaceResolver;

class DomainNamespaceResolverTest extends TestCase
{
    public function test_resolves_namespaces_correctly()
    {
        $resolver = new DomainNamespaceResolver('App\\Domain');
        
        $this->assertEquals('App\\Domain\\User\\Entities', $resolver->resolve('User', 'entity'));
        $this->assertEquals('App\\Domain\\User\\Repositories', $resolver->resolve('User', 'repository'));
        $this->assertEquals('App\\Domain\\User\\Repositories', $resolver->resolve('User', 'repository_interface'));
        $this->assertEquals('App\\Domain\\User\\Services', $resolver->resolve('User', 'service'));
        $this->assertEquals('App\\Domain\\User\\DTOs', $resolver->resolve('User', 'dto'));
    }

    public function test_resolves_paths_correctly()
    {
        $resolver = new DomainNamespaceResolver('App\\Domain');
        $basePath = '/var/www/app/Domain';
        
        $path = $resolver->getPathForNamespace('App\\Domain\\User\\Entities', $basePath);
        
        // Use directory separator for platform independence
        $expected = '/var/www/app/Domain' . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'User' . DIRECTORY_SEPARATOR . 'Entities';
        
        $this->assertEquals($expected, $path);
    }
}
