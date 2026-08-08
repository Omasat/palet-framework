<?php

declare(strict_types=1);

namespace Tests\Routing\UrlGenerator;

use PHPUnit\Framework\TestCase;
use Palet\Framework\Routing\UrlGenerator\SignedUrlGenerator;
use Palet\Framework\Contracts\Routing\UrlGenerator\UrlGeneratorInterface;

class SignedUrlGeneratorTest extends TestCase
{
    public function test_generates_signed_url()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('route')->willReturn('http://localhost/verify/1');
        
        $signedGenerator = new SignedUrlGenerator($urlGenerator, 'secret');
        
        $signedUrl = $signedGenerator->signedRoute('verify', ['id' => 1]);
        
        $this->assertStringContainsString('?signature=', $signedUrl);
        $this->assertStringStartsWith('http://localhost/verify/1?signature=', $signedUrl);
    }

    public function test_validates_signature()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('route')->willReturn('http://localhost/verify/1');
        
        $signedGenerator = new SignedUrlGenerator($urlGenerator, 'secret');
        
        $signedUrl = $signedGenerator->signedRoute('verify', ['id' => 1]);
        
        // Extract signature
        parse_str(parse_url($signedUrl, PHP_URL_QUERY), $query);
        
        $this->assertTrue($signedGenerator->hasValidSignature($signedUrl, $query));
    }

    public function test_rejects_invalid_signature()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $signedGenerator = new SignedUrlGenerator($urlGenerator, 'secret');
        
        $this->assertFalse($signedGenerator->hasValidSignature('http://localhost/verify/1?signature=invalid', ['signature' => 'invalid']));
    }
}
