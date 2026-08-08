<?php

declare(strict_types=1);

namespace Palet\Framework\Security\Cors;

use Palet\Framework\Contracts\Security\CorsManagerInterface;
use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;

class CorsManager implements CorsManagerInterface
{
    protected array $options;

    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            'allowed_origins' => ['*'],
            'allowed_methods' => ['*'],
            'allowed_headers' => ['*'],
            'exposed_headers' => [],
            'max_age' => 0,
            'supports_credentials' => false,
        ], $options);
    }

    public function handle(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $origin = $request->getHeaderLine('Origin');

        if (!$origin) {
            return $response;
        }

        if ($this->isOriginAllowed($origin)) {
            $response = $response->withHeader('Access-Control-Allow-Origin', $origin);

            if ($this->options['supports_credentials']) {
                $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
            }

            if (!empty($this->options['exposed_headers'])) {
                $response = $response->withHeader('Access-Control-Expose-Headers', implode(', ', $this->options['exposed_headers']));
            }
        }

        return $response;
    }

    public function isPreflightRequest(RequestInterface $request): bool
    {
        return $request->getMethod() === 'OPTIONS' && $request->hasHeader('Access-Control-Request-Method');
    }

    public function configurePreflightResponse(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $origin = $request->getHeaderLine('Origin');
        
        if (!$origin || !$this->isOriginAllowed($origin)) {
            return $response;
        }
        
        $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
        
        if ($this->options['supports_credentials']) {
            $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }
        
        $allowedMethods = $this->options['allowed_methods'];
        if (in_array('*', $allowedMethods)) {
            $reqMethod = $request->getHeaderLine('Access-Control-Request-Method');
            $response = $response->withHeader('Access-Control-Allow-Methods', $reqMethod ?: 'GET, POST, OPTIONS');
        } else {
            $response = $response->withHeader('Access-Control-Allow-Methods', implode(', ', $allowedMethods));
        }
        
        $allowedHeaders = $this->options['allowed_headers'];
        if (in_array('*', $allowedHeaders)) {
            $reqHeaders = $request->getHeaderLine('Access-Control-Request-Headers');
            if ($reqHeaders) {
                $response = $response->withHeader('Access-Control-Allow-Headers', $reqHeaders);
            }
        } else {
            $response = $response->withHeader('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
        }
        
        if ($this->options['max_age'] > 0) {
            $response = $response->withHeader('Access-Control-Max-Age', (string) $this->options['max_age']);
        }
        
        return $response;
    }

    protected function isOriginAllowed(string $origin): bool
    {
        if (in_array('*', $this->options['allowed_origins'])) {
            return true;
        }

        return in_array($origin, $this->options['allowed_origins']);
    }
}
