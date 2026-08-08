<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Response\Builders;

use Palet\Framework\Contracts\Http\Response\JsonResponseInterface;

class JsonResponseBuilder extends AbstractResponseBuilder implements JsonResponseInterface
{
    protected mixed $data;
    protected int $encodingOptions = JSON_UNESCAPED_UNICODE;

    public function __construct(mixed $data = [])
    {
        $this->data = $data;
        $this->headers['Content-Type'] = 'application/json';
    }

    public function setEncodingOptions(int $options): static
    {
        $this->encodingOptions = $options;
        return $this;
    }

    protected function getContent(): string
    {
        return json_encode($this->data, $this->encodingOptions) ?: '{}';
    }
}
