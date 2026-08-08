<?php

declare(strict_types=1);

namespace Palet\Framework\View\Html;

use Palet\Framework\Contracts\View\Html\HtmlStringInterface;
use Stringable;

class HtmlString implements HtmlStringInterface, Stringable
{
    protected string $html;

    public function __construct(string $html)
    {
        $this->html = $html;
    }

    public function toHtml(): string
    {
        return $this->html;
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }
}
