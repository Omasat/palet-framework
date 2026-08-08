<?php

declare(strict_types=1);

namespace Palet\Framework\Console\Discovery;

class CommandMetadata
{
    public function __construct(
        public readonly string $name,
        public readonly string $class,
        public readonly string $description = '',
        public readonly bool $hidden = false
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['class'],
            $data['description'] ?? '',
            $data['hidden'] ?? false
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'class' => $this->class,
            'description' => $this->description,
            'hidden' => $this->hidden,
        ];
    }
}
