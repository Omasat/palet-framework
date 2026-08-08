<?php

declare(strict_types=1);

namespace Palet\Framework\Contracts\Feature;

interface CapabilityInterface
{
    public function getName(): string;
    public function getDescription(): string;
    public function getRequiredFeatures(): array;
}
