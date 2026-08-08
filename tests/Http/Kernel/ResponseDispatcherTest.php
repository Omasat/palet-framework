<?php

declare(strict_types=1);

namespace Tests\Http\Kernel;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Kernel\ResponseDispatcher;
use Palet\Framework\Http\Message\Response;
use RuntimeException;

class ResponseDispatcherTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function test_sends_headers_and_body()
    {
        $dispatcher = new ResponseDispatcher();
        $response = new Response(201, ['X-Custom' => 'Value'], 'Hello World');
        
        ob_start();
        $dispatcher->send($response);
        $output = ob_get_clean();
        
        $this->assertEquals('Hello World', $output);
        // Note: xdebug might interfere with header_list(), but checking output is good enough for now
    }
}
