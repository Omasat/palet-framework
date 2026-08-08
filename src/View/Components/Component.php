<?php

declare(strict_types=1);

namespace Palet\Framework\View\Components;

use Palet\Framework\Contracts\View\ComponentInterface;
use Palet\Framework\Contracts\View\ViewInterface;
use Palet\Framework\Contracts\View\AttributeBagInterface;

abstract class Component implements ComponentInterface
{
    public string $componentName;
    public AttributeBagInterface $attributes;

    public function withName(string $name): self
    {
        $this->componentName = $name;

        return $this;
    }

    public function withAttributes(array $attributes): self
    {
        $this->attributes = $this->attributes ?? new AttributeBag();
        $this->attributes = $this->attributes->merge($attributes);

        return $this;
    }

    public function data(): array
    {
        $data = [];

        // Extract public properties to pass them to the view
        $class = new \ReflectionClass($this);
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $data[$property->getName()] = $property->getValue($this);
        }
        
        $data['attributes'] = $this->attributes;
        
        return $data;
    }
}
