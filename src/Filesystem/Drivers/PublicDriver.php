<?php

declare(strict_types=1);

namespace Palet\Framework\Filesystem\Drivers;

class PublicDriver extends LocalDriver
{
    protected string $url;

    public function __construct(string $root, string $url)
    {
        parent::__construct($root);
        $this->url = rtrim($url, '/');
    }

    public function url(string $path): string
    {
        return $this->url . '/' . ltrim(str_replace('\\', '/', $path), '/');
    }

    public function temporaryUrl(string $path, \DateTimeInterface $expiration, array $options = []): string
    {
        // Public disk'te geçici link mantığı genelde sadece tam path'i döndürmektir 
        // ya da signature eklemektir. Basitçe url döndürüyoruz.
        return $this->url($path);
    }
}
