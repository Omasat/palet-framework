<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Message;

use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Http\Message\UriInterface;
use Palet\Framework\Contracts\Http\Message\StreamInterface;

class Request implements RequestInterface
{
    use MessageTrait;

    protected string $method = 'GET';
    protected ?string $requestTarget = null;
    protected ?UriInterface $uri = null;
    
    protected array $serverParams = [];
    protected array $cookieParams = [];
    protected array $queryParams = [];
    protected array $uploadedFiles = [];
    protected null|array|object $parsedBody = null;

    public static function createFromGlobals(): static
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $requestUri;
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$headerName] = $value;
            }
        }
        $body = file_get_contents('php://input');

        $request = new static($method, $uri, $headers, $body);
        $request->serverParams = $_SERVER;
        $request->cookieParams = $_COOKIE;
        $request->queryParams = $_GET;
        $request->parsedBody = $_POST;
        $request->uploadedFiles = $_FILES;

        return $request;
    }

    public function __construct(
        string $method = 'GET',
        string|UriInterface $uri = '',
        array $headers = [],
        mixed $body = null,
        string $version = '1.1'
    ) {
        $this->method = strtoupper($method);
        $this->uri = $uri instanceof UriInterface ? $uri : new Uri($uri);
        
        foreach ($headers as $name => $value) {
            $this->headerNames[strtolower($name)] = $name;
            $this->headers[$name] = (array) $value;
        }
        
        $this->protocolVersion = $version;
        
        if ($body !== null) {
            if ($body instanceof StreamInterface) {
                $this->body = $body;
            } elseif (is_string($body)) {
                $this->body = new Stream('php://temp', 'r+');
                $this->body->write($body);
                $this->body->rewind();
            } else {
                $this->body = new Stream($body, 'r+');
            }
        }
    }

    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        if (!$this->uri) {
            return '/';
        }

        $target = $this->uri->getPath();
        if ($target === '') {
            $target = '/';
        }
        
        if ($this->uri->getQuery() !== '') {
            $target .= '?' . $this->uri->getQuery();
        }
        
        return $target;
    }

    public function withRequestTarget(string $requestTarget): static
    {
        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): static
    {
        $new = clone $this;
        $new->method = strtoupper($method);
        return $new;
    }

    public function getUri(): UriInterface
    {
        if (!$this->uri) {
            $this->uri = new Uri();
        }
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): static
    {
        $new = clone $this;
        $new->uri = $uri;
        
        if (!$preserveHost || !$this->hasHeader('Host')) {
            $host = $uri->getHost();
            if ($host !== '') {
                $port = $uri->getPort();
                if ($port !== null) {
                    $host .= ':' . $port;
                }
                
                $new = $new->withHeader('Host', $host);
            }
        }
        
        return $new;
    }
    
    public function getServerParams(): array { return $this->serverParams; }
    
    public function getCookieParams(): array { return $this->cookieParams; }
    
    public function withCookieParams(array $cookies): static
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }
    
    public function getQueryParams(): array { return $this->queryParams; }
    
    public function withQueryParams(array $query): static
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }
    
    public function getUploadedFiles(): array { return $this->uploadedFiles; }
    
    public function withUploadedFiles(array $uploadedFiles): static
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }
    
    public function getParsedBody(): null|array|object { return $this->parsedBody; }
    
    public function withParsedBody(null|array|object $data): static
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }
}
