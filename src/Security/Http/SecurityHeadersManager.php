<?php

declare(strict_types=1);

namespace Palet\Framework\Security\Http;

use Palet\Framework\Contracts\Http\Message\ResponseInterface;

class SecurityHeadersManager
{
    protected array $headers;

    public function __construct(array $headers = [])
    {
        $this->headers = array_merge([
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Content-Security-Policy' => "default-src 'self'",
        ], $headers);
    }

    public function apply(ResponseInterface $response): ResponseInterface
    {
        foreach ($this->headers as $header => $value) {
            if ($value !== false && $value !== null) {
                $response = $response->withHeader($header, $value);
            }
        }

        return $response;
    }
}
