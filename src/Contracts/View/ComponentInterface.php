<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\View;

interface ComponentInterface
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): string|ViewInterface;

    /**
     * Set the component aliases.
     */
    public function withName(string $name): self;

    /**
     * Set the extra attributes that the component should make available.
     */
    public function withAttributes(array $attributes): self;
}
