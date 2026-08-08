<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\View\Html;

interface HtmlBuilderInterface
{
    /**
     * Generate an HTML element.
     */
    public function tag(string $tag, string|HtmlStringInterface $content = '', array $attributes = []): HtmlElementInterface;

    // A few common tags for fluent API
    public function div(string|HtmlStringInterface $content = '', array $attributes = []): HtmlElementInterface;
    public function span(string|HtmlStringInterface $content = '', array $attributes = []): HtmlElementInterface;
    public function a(string $url, string|HtmlStringInterface $title = '', array $attributes = []): HtmlElementInterface;
    public function img(string $url, string $alt = '', array $attributes = []): HtmlElementInterface;
}
