<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http\Response;

use Palet\Framework\Contracts\Http\Message\ResponseInterface;

interface ResponseBuilderInterface
{
    /**
     * Add a header to the response.
     */
    public function header(string $key, string $value): static;

    /**
     * Add multiple headers to the response.
     */
    public function withHeaders(array $headers): static;

    /**
     * Set the status code for the response.
     */
    public function status(int $status): static;

    /**
     * Build and return the PSR-7 ResponseInterface object.
     */
    public function build(): ResponseInterface;
}
