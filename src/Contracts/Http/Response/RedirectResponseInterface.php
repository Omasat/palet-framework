<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http\Response;

interface RedirectResponseInterface extends ResponseBuilderInterface
{
    /**
     * Flash data to the session before redirecting (for future use).
     */
    public function with(string|array $key, mixed $value = null): static;
}
