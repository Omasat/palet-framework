<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Response\Builders;

class HtmlResponseBuilder extends AbstractResponseBuilder
{
    protected string $html;

    public function __construct(string $html = '')
    {
        $this->html = $html;
        $this->headers['Content-Type'] = 'text/html; charset=UTF-8';
    }

    protected function getContent(): string
    {
        return $this->html;
    }
}
