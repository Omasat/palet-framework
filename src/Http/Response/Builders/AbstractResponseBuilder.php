<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Response\Builders;

use Palet\Framework\Contracts\Http\Response\ResponseBuilderInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Http\Message\Response;

abstract class AbstractResponseBuilder implements ResponseBuilderInterface
{
    protected int $status = 200;
    protected array $headers = [];

    public function header(string $key, string $value): static
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function withHeaders(array $headers): static
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }
        return $this;
    }

    public function status(int $status): static
    {
        $this->status = $status;
        return $this;
    }

    abstract protected function getContent(): string;

    public function build(): ResponseInterface
    {
        return new Response($this->status, $this->headers, $this->getContent());
    }
}
