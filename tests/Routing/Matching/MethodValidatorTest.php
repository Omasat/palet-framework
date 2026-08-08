<?php

declare(strict_types=1);

namespace Tests\Routing\Matching;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\Matching\MethodValidator;
use Palet\Framework\Routing\Route;
use Palet\Framework\Http\Message\Request;

class MethodValidatorTest extends TestCase
{
    public function test_matches_valid_method()
    {
        $validator = new MethodValidator();
        $route = new Route(['POST', 'PUT'], '/users', 'action');
        
        $request1 = new Request('POST', '/users');
        $this->assertTrue($validator->matches($route, $request1));
        
        $request2 = new Request('GET', '/users');
        $this->assertFalse($validator->matches($route, $request2));
    }
}
