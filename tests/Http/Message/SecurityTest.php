<?php

declare(strict_types=1);

namespace Tests\Http\Message;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Http\Message\Request;
use InvalidArgumentException;

class SecurityTest extends TestCase
{
    public function test_crlf_injection_protection()
    {
        $request = new Request();
        
        $this->expectException(InvalidArgumentException::class);
        $request->withHeader('X-Injected', "Valid\r\nInjected: Header");
    }

    public function test_crlf_injection_protection_added_header()
    {
        $request = new Request();
        
        $this->expectException(InvalidArgumentException::class);
        $request->withAddedHeader('X-Injected', "Valid\nInjected: Header");
    }
}
