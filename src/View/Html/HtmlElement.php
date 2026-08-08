<?php

declare(strict_types=1);

namespace Palet\Framework\View\Html;

use Palet\Framework\Contracts\View\Html\HtmlElementInterface;
use Palet\Framework\Contracts\View\Html\HtmlStringInterface;

class HtmlElement implements HtmlElementInterface
{
    protected string $tag;
    protected array $attributes = [];
    protected string $content = '';

    public function __construct(string $tag, string|HtmlStringInterface $content = '', array $attributes = [])
    {
        $this->tag = $tag;
        $this->content = $content instanceof HtmlStringInterface ? $content->toHtml() : $this->escape($content);
        
        foreach ($attributes as $key => $value) {
            $this->attribute($key, $value);
        }
    }

    public function attribute(string $key, string|bool|null $value = true): self
    {
        if ($value === null || $value === false) {
            unset($this->attributes[$key]);
            return $this;
        }

        if ($key === 'class' && isset($this->attributes['class'])) {
            $this->attributes['class'] .= ' ' . $value;
        } else {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    public function class(string $class): self
    {
        return $this->attribute('class', $class);
    }

    public function id(string $id): self
    {
        return $this->attribute('id', $id);
    }

    public function content(string|HtmlStringInterface $content): self
    {
        $this->content = $content instanceof HtmlStringInterface ? $content->toHtml() : $this->escape($content);
        return $this;
    }

    public function toHtml(): string
    {
        $attrs = $this->buildAttributes();
        
        if (in_array(strtolower($this->tag), ['input', 'meta', 'img', 'link', 'hr', 'br'])) {
            return '<' . $this->tag . $attrs . '>';
        }
        
        return '<' . $this->tag . $attrs . '>' . $this->content . '</' . $this->tag . '>';
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }

    protected function buildAttributes(): string
    {
        if (empty($this->attributes)) {
            return '';
        }

        $html = '';
        foreach ($this->attributes as $key => $value) {
            if ($value === true) {
                $html .= ' ' . $key;
            } else {
                $html .= ' ' . $key . '="' . $this->escape((string) $value) . '"';
            }
        }
        return $html;
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
