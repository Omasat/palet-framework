<?php

declare(strict_types=1);

namespace Palet\Framework\Feature;

use Palet\Framework\Contracts\Feature\CapabilityInterface;

class FeatureManifest implements CapabilityInterface
{
    public function __construct(
        protected string $name,
        protected string $description,
        protected array $requiredFeatures = []
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRequiredFeatures(): array
    {
        return $this->requiredFeatures;
    }
}
