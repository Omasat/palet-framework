<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Response;

use Palet\Framework\Contracts\Http\Response\ResponseFactoryInterface;
use Palet\Framework\Contracts\Http\Message\ResponseInterface;
use Palet\Framework\Contracts\Http\Response\ResponseBuilderInterface;
use Palet\Framework\Contracts\Http\Response\JsonResponseInterface;
use Palet\Framework\Contracts\Http\Response\RedirectResponseInterface;
use Palet\Framework\Http\Message\Response;
use Palet\Framework\Http\Response\Builders\JsonResponseBuilder;
use Palet\Framework\Http\Response\Builders\HtmlResponseBuilder;
use Palet\Framework\Http\Response\Builders\TextResponseBuilder;
use Palet\Framework\Http\Response\Builders\XmlResponseBuilder;
use Palet\Framework\Http\Response\Builders\RedirectResponseBuilder;
use Palet\Framework\Http\Response\Builders\FileResponseBuilder;
use Palet\Framework\Http\Response\Builders\DownloadResponseBuilder;

class ResponseFactory implements ResponseFactoryInterface
{
    public function make(mixed $content = '', int $status = 200, array $headers = []): ResponseInterface
    {
        return new Response($status, $headers, (string) $content);
    }

    public function json(mixed $data = [], int $status = 200, array $headers = []): JsonResponseInterface
    {
        return (new JsonResponseBuilder($data))->status($status)->withHeaders($headers);
    }

    public function html(string $html, int $status = 200, array $headers = []): ResponseBuilderInterface
    {
        return (new HtmlResponseBuilder($html))->status($status)->withHeaders($headers);
    }

    public function text(string $text, int $status = 200, array $headers = []): ResponseBuilderInterface
    {
        return (new TextResponseBuilder($text))->status($status)->withHeaders($headers);
    }

    public function xml(string $xml, int $status = 200, array $headers = []): ResponseBuilderInterface
    {
        return (new XmlResponseBuilder($xml))->status($status)->withHeaders($headers);
    }

    public function redirect(string $url, int $status = 302, array $headers = []): RedirectResponseInterface
    {
        return (new RedirectResponseBuilder($url, $status))->withHeaders($headers);
    }

    public function download(string $file, ?string $name = null, array $headers = []): ResponseBuilderInterface
    {
        return (new DownloadResponseBuilder($file, $name))->withHeaders($headers);
    }

    public function file(string $file, array $headers = []): ResponseBuilderInterface
    {
        return (new FileResponseBuilder($file))->withHeaders($headers);
    }

    public function noContent(int $status = 204, array $headers = []): ResponseInterface
    {
        return new Response($status, $headers, '');
    }

    public function notFound(string $message = 'Not Found', array $headers = []): ResponseInterface
    {
        return (new TextResponseBuilder($message))->status(404)->withHeaders($headers)->build();
    }
}
