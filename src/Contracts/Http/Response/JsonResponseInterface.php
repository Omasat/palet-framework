<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http\Response;

interface JsonResponseInterface extends ResponseBuilderInterface
{
    /**
     * Set the JSON encoding options.
     */
    public function setEncodingOptions(int $options): static;
}
