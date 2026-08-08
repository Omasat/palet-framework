<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Kernel;

use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use RuntimeException;

class ResponseDispatcher
{
    public function send(ResponseInterface $response): void
    {
        if (headers_sent()) {
            throw new RuntimeException('Headers have already been sent.');
        }

        $this->sendHeaders($response);
        $this->sendBody($response);
    }

    protected function sendHeaders(ResponseInterface $response): void
    {
        $version = $response->getProtocolVersion();
        $statusCode = $response->getStatusCode();
        $reasonPhrase = $response->getReasonPhrase();
        
        header(sprintf('HTTP/%s %s %s', $version, $statusCode, $reasonPhrase), true, $statusCode);

        foreach ($response->getHeaders() as $name => $values) {
            $first = strtolower($name) !== 'set-cookie';
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), $first);
                $first = false;
            }
        }
    }

    protected function sendBody(ResponseInterface $response): void
    {
        $body = $response->getBody();
        
        if ($body->isSeekable()) {
            $body->rewind();
        }

        while (!$body->eof()) {
            echo $body->read(8192); // 8KB chunks
        }
    }
}
