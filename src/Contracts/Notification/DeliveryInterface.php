<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Notification;

interface DeliveryInterface
{
    public function getStatus(): string;
    public function getError(): ?string;
}
