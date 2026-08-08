<?php

declare(strict_types=1);

namespace Palet\Framework\Http\Response\Builders;

use Palet\Framework\Contracts\Http\Response\RedirectResponseInterface;

class RedirectResponseBuilder extends AbstractResponseBuilder implements RedirectResponseInterface
{
    public function __construct(string $url, int $status = 302)
    {
        $this->status = $status;
        $this->headers['Location'] = $url;
    }

    public function with(string|array $key, mixed $value = null): static
    {
        // For future session flashing
        return $this;
    }

    protected function getContent(): string
    {
        $url = htmlspecialchars($this->headers['Location'] ?? '', ENT_QUOTES, 'UTF-8');
        return sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="0;url=\'%1$s\'" />
        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', $url);
    }
}
