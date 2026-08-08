<?php

declare(strict_types=1);

namespace Tests\Generator;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Generator\PlaceholderResolver;

class PlaceholderResolverTest extends TestCase
{
    public function test_resolves_placeholders()
    {
        $resolver = new PlaceholderResolver();
        
        $content = 'namespace {{ Namespace }}; class {{ ClassName }}';
        $variables = [
            'Namespace' => 'App\\Http',
            'ClassName' => 'TestController'
        ];
        
        $resolved = $resolver->resolve($content, $variables);
        
        $this->assertEquals('namespace App\\Http; class TestController', $resolved);
    }
}
