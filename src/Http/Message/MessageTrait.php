<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Message;

use Palet\Framework\Contracts\Http\Message\StreamInterface;
use InvalidArgumentException;

trait MessageTrait
{
    protected string $protocolVersion = '1.1';
    
    /** @var array<string, array<string>> */
    protected array $headers = [];
    
    /** @var array<string, string> */
    protected array $headerNames = [];
    
    protected ?StreamInterface $body = null;

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): static
    {
        if ($this->protocolVersion === $version) {
            return $this;
        }
        
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headerNames[strtolower($name)]);
    }

    public function getHeader(string $name): array
    {
        $normalized = strtolower($name);
        
        if (!isset($this->headerNames[$normalized])) {
            return [];
        }
        
        $header = $this->headerNames[$normalized];
        return $this->headers[$header];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(',', $this->getHeader($name));
    }

    public function withHeader(string $name, string|array $value): static
    {
        $this->assertValidHeaderValue($value);
        
        $normalized = strtolower($name);
        $new = clone $this;
        
        if (isset($new->headerNames[$normalized])) {
            unset($new->headers[$new->headerNames[$normalized]]);
        }
        
        $new->headerNames[$normalized] = $name;
        $new->headers[$name] = (array) $value;
        
        return $new;
    }

    public function withAddedHeader(string $name, string|array $value): static
    {
        $this->assertValidHeaderValue($value);
        
        $new = clone $this;
        $normalized = strtolower($name);
        
        if (isset($new->headerNames[$normalized])) {
            $header = $new->headerNames[$normalized];
            $new->headers[$header] = array_merge($new->headers[$header], (array) $value);
        } else {
            $new->headerNames[$normalized] = $name;
            $new->headers[$name] = (array) $value;
        }
        
        return $new;
    }

    public function withoutHeader(string $name): static
    {
        $normalized = strtolower($name);
        
        if (!isset($this->headerNames[$normalized])) {
            return $this;
        }
        
        $new = clone $this;
        $header = $new->headerNames[$normalized];
        unset($new->headers[$header], $new->headerNames[$normalized]);
        
        return $new;
    }

    public function getBody(): StreamInterface
    {
        if (!$this->body) {
            $this->body = new Stream('php://temp', 'r+');
        }
        
        return $this->body;
    }

    public function withBody(StreamInterface $body): static
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }

    protected function assertValidHeaderValue(string|array $value): void
    {
        $values = (array) $value;
        foreach ($values as $val) {
            if (preg_match("#\r?\n#", (string) $val)) {
                throw new InvalidArgumentException('Header values cannot contain CRLF sequences.');
            }
        }
    }
}
