<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\View\Html;

interface HtmlStringInterface
{
    /**
     * Get the HTML string.
     */
    public function toHtml(): string;
}
