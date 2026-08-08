<?php

declare(strict_types=1);

namespace Palet\Framework\View\Html;

use Palet\Framework\Contracts\View\Html\HtmlBuilderInterface;
use Palet\Framework\Contracts\View\Html\HtmlElementInterface;
use Palet\Framework\Contracts\View\Html\HtmlStringInterface;

class HtmlBuilder implements HtmlBuilderInterface
{
    public function tag(string $tag, string|HtmlStringInterface $content = '', array $attributes = []): HtmlElementInterface
    {
        return new HtmlElement($tag, $content, $attributes);
    }

    public function div(string|HtmlStringInterface $content = '', array $attributes = []): HtmlElementInterface
    {
        return $this->tag('div', $content, $attributes);
    }

    public function span(string|HtmlStringInterface $content = '', array $attributes = []): HtmlElementInterface
    {
        return $this->tag('span', $content, $attributes);
    }

    public function a(string $url, string|HtmlStringInterface $title = '', array $attributes = []): HtmlElementInterface
    {
        $attributes['href'] = $url;
        return $this->tag('a', $title, $attributes);
    }

    public function img(string $url, string $alt = '', array $attributes = []): HtmlElementInterface
    {
        $attributes['src'] = $url;
        if ($alt !== '') {
            $attributes['alt'] = $alt;
        }
        return $this->tag('img', '', $attributes);
    }
    
    // Magic method for other tags
    public function __call($method, $parameters)
    {
        $content = $parameters[0] ?? '';
        $attributes = $parameters[1] ?? [];
        return $this->tag($method, $content, $attributes);
    }
}
