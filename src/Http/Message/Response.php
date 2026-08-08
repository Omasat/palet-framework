<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Message;

use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Contracts\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    use MessageTrait;

    protected int $statusCode = 200;
    protected string $reasonPhrase = '';

    protected array $phrases = [
        200 => 'OK',
        201 => 'Created',
        301 => 'Moved Permanently',
        302 => 'Found',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
    ];

    public function __construct(
        int $status = 200,
        array $headers = [],
        mixed $body = null,
        string $version = '1.1',
        string $reason = ''
    ) {
        $this->statusCode = $status;
        
        foreach ($headers as $name => $value) {
            $this->headerNames[strtolower($name)] = $name;
            $this->headers[$name] = (array) $value;
        }
        
        $this->protocolVersion = $version;
        $this->reasonPhrase = $reason !== '' ? $reason : ($this->phrases[$status] ?? '');
        
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

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        $new = clone $this;
        $new->statusCode = $code;
        $new->reasonPhrase = $reasonPhrase !== '' ? $reasonPhrase : ($this->phrases[$code] ?? '');
        return $new;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}
