<?php

declare(strict_types=1);

namespace Tests\Routing\UrlGenerator;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\UrlGenerator\UrlGenerator;
use Palet\Framework\Routing\UrlGenerator\RouteUrlGenerator;
use Palet\Framework\Routing\UrlGenerator\UrlDefaults;
use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\UriInterface;

class UrlGeneratorTest extends TestCase
{
    public function test_to_generates_absolute_url()
    {
        $routeGenerator = $this->createMock(RouteUrlGenerator::class);
        $defaults = new UrlDefaults();
        
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getScheme')->willReturn('http');
        $uri->method('getHost')->willReturn('palet.test');
        
        $request = $this->createMock(RequestInterface::class);
        $request->method('getUri')->willReturn($uri);
        
        $generator = new UrlGenerator($routeGenerator, $defaults, $request);
        
        $url = $generator->to('/css/app.css');
        $this->assertEquals('http://palet.test/css/app.css', $url);
        
        $secureUrl = $generator->to('/js/app.js', [], true);
        $this->assertEquals('https://palet.test/js/app.js', $secureUrl);
    }
}
