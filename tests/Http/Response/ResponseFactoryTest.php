<?php

declare(strict_types=1);

namespace Tests\Http\Response;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Response\ResponseFactory;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;

class ResponseFactoryTest extends TestCase
{
    public function test_make_returns_response()
    {
        $factory = new ResponseFactory();
        $response = $factory->make('Hello World', 201, ['X-Custom' => 'Value']);
        
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('Hello World', $response->getBody()->getContents());
        $this->assertEquals('Value', $response->getHeaderLine('X-Custom'));
    }

    public function test_json_builder_creates_json_response()
    {
        $factory = new ResponseFactory();
        $response = $factory->json(['key' => 'value'])->build();
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"key":"value"}', $response->getBody()->getContents());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function test_html_builder_creates_html_response()
    {
        $factory = new ResponseFactory();
        $response = $factory->html('<h1>Hi</h1>')->build();
        
        $this->assertEquals('<h1>Hi</h1>', $response->getBody()->getContents());
        $this->assertEquals('text/html; charset=UTF-8', $response->getHeaderLine('Content-Type'));
    }

    public function test_text_builder_creates_text_response()
    {
        $factory = new ResponseFactory();
        $response = $factory->text('Hi')->build();
        
        $this->assertEquals('Hi', $response->getBody()->getContents());
        $this->assertEquals('text/plain; charset=UTF-8', $response->getHeaderLine('Content-Type'));
    }

    public function test_xml_builder_creates_xml_response()
    {
        $factory = new ResponseFactory();
        $response = $factory->xml('<data></data>')->build();
        
        $this->assertEquals('<data></data>', $response->getBody()->getContents());
        $this->assertEquals('application/xml; charset=UTF-8', $response->getHeaderLine('Content-Type'));
    }

    public function test_redirect_builder_creates_redirect_response()
    {
        $factory = new ResponseFactory();
        $response = $factory->redirect('/home', 301)->build();
        
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('/home', $response->getHeaderLine('Location'));
    }

    public function test_no_content_returns_empty_response()
    {
        $factory = new ResponseFactory();
        $response = $factory->noContent();
        
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('', $response->getBody()->getContents());
    }

    public function test_not_found_returns_404_response()
    {
        $factory = new ResponseFactory();
        $response = $factory->notFound('Oops');
        
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Oops', $response->getBody()->getContents());
    }
}
