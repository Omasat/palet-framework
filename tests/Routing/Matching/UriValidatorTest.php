<?php

declare(strict_types=1);

namespace Tests\Routing\Matching;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\Matching\UriValidator;
use Palet\Framework\Routing\Route;
use Palet\Framework\Http\Message\Request;

class UriValidatorTest extends TestCase
{
    public function test_matches_valid_uri()
    {
        $validator = new UriValidator();
        $route = new Route('GET', '/users/{id}', 'action');
        
        $request = new Request('GET', 'http://example.com/users/123');
        $this->assertTrue($validator->matches($route, $request));
        
        $request2 = new Request('GET', 'http://example.com/users/invalid@id');
        $this->assertFalse($validator->matches($route, $request2));
    }

    public function test_extracts_parameters()
    {
        $validator = new UriValidator();
        $route = new Route('GET', '/posts/{category}/{slug}', 'action');
        
        $request = new Request('GET', 'http://example.com/posts/tech/hello-world');
        
        $parameters = $validator->extractParameters($route, $request);
        
        $this->assertEquals(['category' => 'tech', 'slug' => 'hello-world'], $parameters);
    }
}
