<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\View\Html;

use Stringable;

interface HtmlElementInterface extends HtmlStringInterface, Stringable
{
    /**
     * Add or merge an attribute.
     */
    public function attribute(string $key, string|bool|null $value = true): self;

    /**
     * Add a class to the element.
     */
    public function class(string $class): self;

    /**
     * Add an id to the element.
     */
    public function id(string $id): self;

    /**
     * Set the inner content of the element.
     */
    public function content(string|HtmlStringInterface $content): self;
}
