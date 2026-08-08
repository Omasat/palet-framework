<?php

declare(strict_types=1);

namespace Tests\Generator\Entity;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Generator\Entity\NamingConventionResolver;

class NamingConventionResolverTest extends TestCase
{
    public function test_to_plural()
    {
        $resolver = new NamingConventionResolver();
        
        $this->assertEquals('users', $resolver->toPlural('user'));
        $this->assertEquals('categories', $resolver->toPlural('category'));
        $this->assertEquals('buses', $resolver->toPlural('bus'));
    }

    public function test_to_singular()
    {
        $resolver = new NamingConventionResolver();
        
        $this->assertEquals('user', $resolver->toSingular('users'));
        $this->assertEquals('category', $resolver->toSingular('categories'));
    }

    public function test_to_snake_case()
    {
        $resolver = new NamingConventionResolver();
        
        $this->assertEquals('user_account', $resolver->toSnakeCase('UserAccount'));
        $this->assertEquals('user_account', $resolver->toSnakeCase('userAccount'));
    }

    public function test_to_camel_case()
    {
        $resolver = new NamingConventionResolver();
        
        $this->assertEquals('userAccount', $resolver->toCamelCase('UserAccount'));
        $this->assertEquals('userAccount', $resolver->toCamelCase('user_account'));
    }

    public function test_to_pascal_case()
    {
        $resolver = new NamingConventionResolver();
        
        $this->assertEquals('UserAccount', $resolver->toPascalCase('userAccount'));
        $this->assertEquals('UserAccount', $resolver->toPascalCase('user_account'));
    }
}
