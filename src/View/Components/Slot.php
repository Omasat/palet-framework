<?php

declare(strict_types=1);

namespace Palet\Framework\View\Components;

use Stringable;

class Slot implements Stringable
{
    protected string $contents;

    public function __construct(string $contents = '')
    {
        $this->contents = $contents;
    }

    public function toHtml(): string
    {
        return $this->contents;
    }

    public function isEmpty(): bool
    {
        return trim($this->contents) === '';
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }
}
