<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http\Message;

interface RequestInterface extends MessageInterface
{
    public function getRequestTarget(): string;
    public function withRequestTarget(string $requestTarget): static;
    
    public function getMethod(): string;
    public function withMethod(string $method): static;
    
    public function getUri(): UriInterface;
    public function withUri(UriInterface $uri, bool $preserveHost = false): static;
    
    public function getServerParams(): array;
    public function getCookieParams(): array;
    public function withCookieParams(array $cookies): static;
    
    public function getQueryParams(): array;
    public function withQueryParams(array $query): static;
    
    public function getUploadedFiles(): array;
    public function withUploadedFiles(array $uploadedFiles): static;
    
    public function getParsedBody(): null|array|object;
    public function withParsedBody(null|array|object $data): static;
}
