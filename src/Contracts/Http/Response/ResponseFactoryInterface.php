<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Http\Response;

use Palet\Framework\Contracts\Http\Message\ResponseInterface;

interface ResponseFactoryInterface
{
    public function make(mixed $content = '', int $status = 200, array $headers = []): ResponseInterface;
    public function json(mixed $data = [], int $status = 200, array $headers = []): JsonResponseInterface;
    public function html(string $html, int $status = 200, array $headers = []): ResponseBuilderInterface;
    public function text(string $text, int $status = 200, array $headers = []): ResponseBuilderInterface;
    public function xml(string $xml, int $status = 200, array $headers = []): ResponseBuilderInterface;
    public function redirect(string $url, int $status = 302, array $headers = []): RedirectResponseInterface;
    public function download(string $file, ?string $name = null, array $headers = []): ResponseBuilderInterface;
    public function file(string $file, array $headers = []): ResponseBuilderInterface;
    public function noContent(int $status = 204, array $headers = []): ResponseInterface;
    public function notFound(string $message = 'Not Found', array $headers = []): ResponseInterface;
}
