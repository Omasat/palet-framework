<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Database\Orm\Model;

interface FillableInterface
{
    /**
     * Fill the model with an array of attributes.
     */
    public function fill(array $attributes): static;

    /**
     * Fill the model with an array of attributes. Force mass assignment.
     */
    public function forceFill(array $attributes): static;
}
