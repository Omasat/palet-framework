<?php

declare(strict_types=1);

namespace Palet\Framework\Routing\UrlGenerator;

use Palet\Framework\Contracts\Routing\UrlGenerator\SignedUrlGeneratorInterface;
use Palet\Framework\Contracts\Routing\UrlGenerator\UrlGeneratorInterface;
use DateTimeInterface;

class SignedUrlGenerator implements SignedUrlGeneratorInterface
{
    protected UrlGeneratorInterface $urlGenerator;
    protected string $secretKey;

    public function __construct(UrlGeneratorInterface $urlGenerator, string $secretKey)
    {
        $this->urlGenerator = $urlGenerator;
        $this->secretKey = $secretKey;
    }

    public function signedRoute(string $name, array $parameters = [], ?DateTimeInterface $expiration = null, bool $absolute = true): string
    {
        if ($expiration) {
            $parameters['expires'] = $expiration->getTimestamp();
        }

        // Generate URL without signature
        $url = $this->urlGenerator->route($name, $parameters, $absolute);
        
        // Generate signature
        $signature = hash_hmac('sha256', $url, $this->secretKey);
        
        // Append signature
        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . 'signature=' . $signature;
    }

    public function temporarySignedRoute(string $name, DateTimeInterface $expiration, array $parameters = [], bool $absolute = true): string
    {
        return $this->signedRoute($name, $parameters, $expiration, $absolute);
    }

    public function hasValidSignature(string $url, array $query): bool
    {
        $signature = $query['signature'] ?? null;
        
        if (!$signature) {
            return false;
        }

        // Check expiration
        if (isset($query['expires'])) {
            $expires = (int) $query['expires'];
            if (time() > $expires) {
                return false;
            }
        }

        // Remove signature from URL to verify
        // The URL could be "http://site.com/foo?expires=123&signature=abc"
        // We need to strip "&signature=abc" or "?signature=abc"
        
        $originalUrl = preg_replace('/(&|\?)signature=[^&]+/', '', $url);
        
        $expectedSignature = hash_hmac('sha256', $originalUrl, $this->secretKey);
        
        return hash_equals($expectedSignature, $signature);
    }
}
