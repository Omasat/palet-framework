<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Response\Builders;

class TextResponseBuilder extends AbstractResponseBuilder
{
    protected string $text;

    public function __construct(string $text = '')
    {
        $this->text = $text;
        $this->headers['Content-Type'] = 'text/plain; charset=UTF-8';
    }

    protected function getContent(): string
    {
        return $this->text;
    }
}
