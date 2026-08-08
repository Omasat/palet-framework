<?php

declare(strict_types=1);

namespace Tests\Http\Message;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Message\Response;

class ResponseTest extends TestCase
{
    public function test_default_values()
    {
        $response = new Response();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    public function test_with_status()
    {
        $response = new Response();
        $new = $response->withStatus(404);
        
        $this->assertEquals(404, $new->getStatusCode());
        $this->assertEquals('Not Found', $new->getReasonPhrase());
        
        $new2 = $new->withStatus(500, 'Custom Error');
        $this->assertEquals('Custom Error', $new2->getReasonPhrase());
    }
}
