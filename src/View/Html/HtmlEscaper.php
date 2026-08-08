<?php

declare(strict_types=1);

namespace Palet\Framework\View\Html;

use Palet\Framework\Contracts\View\Html\HtmlStringInterface;

class HtmlEscaper
{
    public static function escape(mixed $value): string
    {
        if ($value instanceof HtmlStringInterface) {
            return $value->toHtml();
        }

        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
