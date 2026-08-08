<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Notification;

interface TemplateInterface
{
    public function render(string $templateName, array $data): string;
}
