<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Subscription;

interface LicenseInterface
{
    public function getKey(): string;
    public function isValid(): bool;
    public function getExpiresAt(): ?\DateTimeInterface;
    public function getMetadata(): array;
}
