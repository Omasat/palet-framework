<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Response\Builders;

class XmlResponseBuilder extends AbstractResponseBuilder
{
    protected string $xml;

    public function __construct(string $xml = '')
    {
        $this->xml = $xml;
        $this->headers['Content-Type'] = 'application/xml; charset=UTF-8';
    }

    protected function getContent(): string
    {
        return $this->xml;
    }
}
