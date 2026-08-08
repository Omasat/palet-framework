<?php

declare(strict_types=1);

namespace Tests\Routing\UrlGenerator;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\UrlGenerator\RouteUrlGenerator;
use Palet\Framework\Routing\UrlGenerator\UrlDefaults;
use Palet\Framework\Routing\RouteCollection;
use Palet\Framework\Routing\Route;

class RouteUrlGeneratorTest extends TestCase
{
    public function test_generates_url_from_named_route_with_parameters()
    {
        $routes = new RouteCollection();
        $route = (new Route('GET', '/users/{id}/comments/{comment?}', 'UserController@show'))->name('users.comments');
        $routes->add($route);
        
        $generator = new RouteUrlGenerator($routes, new UrlDefaults());
        
        $url = $generator->generate('users.comments', ['id' => 15, 'sort' => 'desc']);
        
        $this->assertEquals('http://localhost/users/15/comments?sort=desc', $url);
        
        $urlWithComment = $generator->generate('users.comments', ['id' => 15, 'comment' => 99]);
        
        $this->assertEquals('http://localhost/users/15/comments/99', $urlWithComment);
    }
}
