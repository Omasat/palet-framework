<?php

declare(strict_types=1);

namespace Tests\Routing\Dispatcher;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\Dispatcher\ActionResultNormalizer;
use Palet\Framework\Http\Message\Response;

class ActionResultNormalizerTest extends TestCase
{
    public function test_normalizes_string()
    {
        $normalizer = new ActionResultNormalizer();
        $response = $normalizer->normalize('Hello');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello', $response->getBody()->getContents());
        $this->assertEquals('text/html; charset=UTF-8', $response->getHeaderLine('Content-Type'));
    }

    public function test_normalizes_array_to_json()
    {
        $normalizer = new ActionResultNormalizer();
        $response = $normalizer->normalize(['success' => true]);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"success":true}', $response->getBody()->getContents());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function test_returns_response_object_as_is()
    {
        $normalizer = new ActionResultNormalizer();
        $original = new Response(201, [], 'Created');
        
        $response = $normalizer->normalize($original);
        
        $this->assertSame($original, $response);
    }

    public function test_normalizes_null()
    {
        $normalizer = new ActionResultNormalizer();
        $response = $normalizer->normalize(null);
        
        $this->assertEquals(204, $response->getStatusCode());
    }
}
